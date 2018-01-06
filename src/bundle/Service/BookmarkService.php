<?php

namespace Edgar\EzUIBookmarkBundle\Service;

use Doctrine\ORM\EntityManager;
use Edgar\EzUIBookmarkBundle\Entity\EdgarEzBookmark;
use Edgar\EzUIBookmarkBundle\Exception\BookmarkException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\TranslatorInterface;
use eZ\Publish\Core\MVC\Symfony\Security\User;

class BookmarkService
{
    /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository|\Edgar\EzUIBookmark\Repository\EdgarEzBookmarkRepository  */
    protected $repository;

    /** @var LocationService  */
    protected $locationService;

    /** @var TranslatorInterface */
    private $translator;

    /** @var TokenStorage */
    private $tokenStorage;

    public function __construct(
        EntityManager $entityManager,
        LocationService $locationService,
        TranslatorInterface $translator,
        TokenStorage $tokenStorage
    ) {
        $this->repository = $entityManager->getRepository(EdgarEzBookmark::class);
        $this->locationService = $locationService;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
    }

    public function alreadyRegistered(int $userId, ?int $locationId): bool
    {
        if ($this->repository->findOneBy(['userId' => $userId, 'locationId' => $locationId])) {
            return true;
        }

        return false;
    }

    public function register(EdgarEzBookmark $data)
    {
        $this->repository->register($data);
    }

    public function unRegister(int $userId, int $locationId)
    {
        $this->repository->unRegister($userId, $locationId);
    }

    public function unRegisterLocation(int $locationId)
    {
        $this->repository->unRegisterLocation($locationId);
    }


    /**
     * @param int|null $locationId
     * @return bool
     * @throws BookmarkException
     */
    public function hasLocationAccess(?int $locationId): bool
    {
        if (!$locationId) {
            throw new BookmarkException(
                $this->translator->trans(
                    'edgar.ezuibookmark.exception.no_location',
                    [],
                    'ezgarezuibookmark'
                )
            );
        }

        try {
            $this->locationService->loadLocation($locationId);
        } catch (UnauthorizedException | NotFoundException $e) {
            throw new BookmarkException(
                $this->translator->trans(
                    'edgar.ezuibookmark.exception.location : %message%',
                    ['message' => $e->getMessage()],
                    'ezgarezuibookmark'
                )
            );
        }

        return true;
    }

    public function listBookmarks(): array
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $apiUser = $user->getAPIUser();

        return $this->repository->findByUserId($apiUser->id);
    }
}
