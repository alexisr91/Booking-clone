<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class) // Annotation 
 */

class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue // Annotation 
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255) // Annotation 
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255) // Annotation 
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="images") // Annotation 
     */
    private $ad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }
}
