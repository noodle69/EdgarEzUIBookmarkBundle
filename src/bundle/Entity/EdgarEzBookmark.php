<?php

namespace Edgar\EzUIBookmarkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgarEzBookmark
 *
 * @ORM\Entity(repositoryClass="Edgar\EzUIBookmark\Repository\EdgarEzBookmarkRepository")
 * @ORM\Table(name="edgar_ez_bookmark")
 */
class EdgarEzBookmark
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="location_id", type="integer", nullable=false)
     * @ORM\Id
     */
    private $locationId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     */
    private $userId;

    /**
     * @param string $name
     * @return EdgarEzBookmark
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $locationId
     * @return EdgarEzBookmark
     */
    public function setLocationId(?int $locationId): self
    {
        $this->locationId = $locationId;
        return $this;
    }

    /**
     * @param int $userId
     * @return EdgarEzBookmark
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
