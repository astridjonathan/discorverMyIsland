<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $alias;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="sites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;



    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $openHour;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $visiteType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="site")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Visit", mappedBy="site")
     */
    private $visits;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->visits = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }


    /**
     * @return Collection|Visit[]
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits[] = $visit;
            $visit->setSite($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->contains($visit)) {
            $this->visits->removeElement($visit);
            // set the owning side to null (unless already changed)
            if ($visit->getSite() === $this) {
                $visit->setSite(null);
            }
        }


        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * @param mixed $siteWeb
     */
    public function setSiteWeb($siteWeb): void
    {
        $this->siteWeb = $siteWeb;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel): void
    {
        $this->tel = $tel;
    }
    public function getOpenHour(): ?string
    {
        return $this->openHour;
    }

    public function setOpenHour(string $openHour): self
    {
        $this->openHour = $openHour;

        return $this;
    }

    public function getVisiteType(): ?string
    {
        return $this->visiteType;
    }

    public function setVisiteType(?string $visiteType): self
    {
        $this->visiteType = $visiteType;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setSite($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getSite() === $this) {
                $comment->setSite(null);
            }
        }

        return $this;
    }


}
