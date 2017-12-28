<?php

namespace Edgar\EzUIBookmarkBundle\Controller;

use Edgar\EzUIBookmark\Form\Factory\FormFactory;
use Edgar\EzUIBookmark\Form\SubmitHandler;
use Edgar\EzUIBookmarkBundle\Entity\EdgarEzBookmark;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use eZ\Publish\API\Repository\Values\User\User as APIUser;

class BookmarkController extends Controller
{
    /** @var FormFactory  */
    protected $formFactory;

    /** @var SubmitHandler  */
    protected $submitHandler;

    /** @var TokenStorage $tokenStorage */
    protected $tokenStorage;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        TokenStorage $tokenStorage
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->tokenStorage = $tokenStorage;
    }

    public function modalAction(): Response
    {
        $form = $this->formFactory->addBookmark(new EdgarEzBookmark());

        return $this->render('@EdgarEzUIBookmark/bookmark/modal_bookmark.html.twig', [
            'form' => $form->createView(),
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
            $result = $this->submitHandler->handle($form, $apiUser, function (EdgarEzBookmark $data, APIUser $apiUser) {
                $data->setUserId($apiUser->id);
                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $data->getLocationId(),
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $locationId = $request->request->has('location_id') ? $request->request->get('location_id') : false;
        if ($locationId) {
            $locationService = $this->container->get('ezpublish.api.repository')->getLocationService();
            try {
                $locationService->loadLocation($locationId);
                return new RedirectResponse($this->generateUrl('_ezpublishLocation', [
                    'locationId' => $locationId,
                ]));
            } catch (UnauthorizedException | NotFoundException $e) {
                return new RedirectResponse($this->generateUrl('ezplatform.dashboard', []));
            }
        }

        return new RedirectResponse($this->generateUrl('ezplatform.dashboard', []));
    }
}
