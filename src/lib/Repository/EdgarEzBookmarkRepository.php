<?php

namespace Edgar\EzUIBookmark\Repository;

use Doctrine\ORM\EntityRepository;
use Edgar\EzUIBookmarkBundle\Entity\EdgarEzBookmark;

class EdgarEzBookmarkRepository extends EntityRepository
{
    public function register(EdgarEzBookmark $data)
    {
        $this->getEntityManager()->persist($data);
        $this->getEntityManager()->flush();
    }

    public function unRegister(int $userId, int $locationId)
    {
        $bookmark = $this->findOneBy(['userId' => $userId, 'locationId' => $locationId]);
        if ($bookmark) {
            $this->getEntityManager()->remove($bookmark);
            $this->getEntityManager()->flush();
        }
    }

    public function unRegisterLocation(int $locationId)
    {
        $bookmarks = $this->findBy(['locationId' => $locationId]);
        if ($bookmarks) {
            foreach ($bookmarks as $bookmark) {
                $this->getEntityManager()->remove($bookmark);
            }

            $this->getEntityManager()->flush();
        }
    }

}
