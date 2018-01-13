<?php

namespace Edgar\EzUIBookmarkBundle\Controller;

use Edgar\EzUIBookmark\Form\Factory\FormFactory;
use Edgar\EzUIBookmark\Form\SubmitHandler;
use Edgar\EzUIBookmarkBundle\Entity\EdgarEzBookmark;
use Edgar\EzUIBookmarkBundle\Exception\BookmarkException;
use Edgar\EzUIBookmarkBundle\Service\BookmarkService;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Symfony\Component\Translation\TranslatorInterface;

class BookmarkController extends Controller
{
    /** @var FormFactory  */
    protected $formFactory;

    /** @var SubmitHandler  */
    protected $submitHandler;

    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    /** @var BookmarkService  */
    protected $bookmarkService;

    /** @var NotificationHandlerInterface $notificationHandler */
    protected $notificationHandler;

    /** @var TranslatorInterface  */
    protected $translator;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TokenStorage $tokenStorage,
        BookmarkService $bookmarkService,
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->tokenStorage = $tokenStorage;
        $this->bookmarkService = $bookmarkService;
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
    }

    public function modalAction(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $formAdd = $this->formFactory->addBookmark(new EdgarEzBookmark());
        $formDelete = $this->formFactory->deleteBookmark(new EdgarEzBookmark());

        return $this->render('@EdgarEzUIBookmark/bookmark/modal_bookmark.html.twig', [
            'form_add' => $formAdd->createView(),
            'form_delete' => $formDelete->createView(),
            'user_id' => $apiUser->id,
        ]);
    }

    public function addAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $form = $this->formFactory->addBookmark(
            new EdgarEzBookmark()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handleAdd($form, $apiUser, function (EdgarEzBookmark $data, APIUser $apiUser) {
                if ($this->bookmarkService->alreadyRegistered($apiUser->id, $data->getLocationId())) {
                    $this->notificationHandler->error(
                        $this->translator->trans(
                            'edgar.ezuibookmark.already_bookmarked',
                            [],
                            'ezgarezuibookmark'
                        )
                    );
                } else {
                    $data->setUserId($apiUser->id);
                    $this->bookmarkService->register($data);
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            'edgar.ezuibookmark.registratered',
                            [],
                            'ezgarezuibookmark'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $data->getLocationId(),
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $locationId = $request->request->get('location_id', null);
        try {
            $this->bookmarkService->hasLocationAccess($locationId);
            return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                'locationId' => $locationId,
            ]));
        } catch (BookmarkException $e) {
            $this->notificationHandler->error(
                $e->getMessage()
            );
            return new RedirectResponse($this->generateUrl('ezplatform.dashboard', []));
        }
    }

    public function deleteAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        $form = $this->formFactory->deleteBookmark(
            new EdgarEzBookmark()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handleDelete($form, $apiUser, function (EdgarEzBookmark $data, APIUser $apiUser) {
                if ($this->bookmarkService->alreadyRegistered($apiUser->id, $data->getLocationId())) {
                    $this->bookmarkService->unRegister($apiUser->id, $data->getLocationId());
                    $this->notificationHandler->success(
                        $this->translator->trans(
                            'edgar.ezuibookmark.unregistratered',
                            [],
                            'ezgarezuibookmark'
                        )
                    );
                } else {
                    $this->notificationHandler->error(
                        $this->translator->trans(
                            'edgar.ezuibookmark.not_bookmarked',
                            [],
                            'ezgarezuibookmark'
                        )
                    );
                }

                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $data->getLocationId(),
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $locationId = $request->request->get('location_id', null);
        try {
            $this->bookmarkService->hasLocationAccess($locationId);
            return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                'locationId' => $locationId,
            ]));
        } catch (BookmarkException $e) {
            $this->notificationHandler->error(
                $e->getMessage()
            );
            return new RedirectResponse($this->generateUrl('ezplatform.dashboard', []));
        }
    }

    public function profileAction(): Response
    {
        $bookmarks = $this->bookmarkService->listBookmarks();

        return $this->render('@EdgarEzUIBookmark/profile/bookmark.html.twig', [
            'bookmarks' => $bookmarks,
        ]);
    }
}
