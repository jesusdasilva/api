<?php

namespace App\Entity;

use App\Entity\Interfaces\Statuable;
use App\Entity\Traits\StatuableTrait;
use App\Repository\AlbumRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={
 *     @Index(name="status_idx", columns={ "status" })
 * })
 * @ORM\Entity(repositoryClass="AlbumRepository")
 */
class Album implements Statuable
{
    use StatuableTrait;

    // -------------------------- >

    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->albumFiles = new ArrayCollection();

        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    // -------------------------- >

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", unique=true)
     */
    private int $id;

    /**
     * @ORM\Column(name="title", type="string", length="255")
     */
    private string $title;

    /**
     * @ORM\Column(name="description", type="string", length="255", nullable=true)
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity="Travel", inversedBy="albums")
     * @ORM\JoinColumn(name="travel_id", referencedColumnName="id")
     */
    private Travel $travelId;

    /**
     * @ORM\Column(name="created_at", type="date")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="date")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Step", inversedBy="albums")
     */
    private Collection $steps;

    // -------------------------- >

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Album
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Album
    {
        $this->description = $description;

        return $this;
    }

    public function getTravelId(): ?Travel
    {
        return $this->travelId;
    }

    public function setTravelId(?Travel $travelId): Album
    {
        $this->travelId = $travelId;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Album
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): Album
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
