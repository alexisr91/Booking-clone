<?php

namespace App\Entity;

use App\Repository\UserRepository; // Il est grisé car il n'est pas utilisé 
use Doctrine\Common\Collections\ArrayCollection; // gestion des relations entre les users 
use Doctrine\Common\Collections\Collection;// gestion des relations entre les users 
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"email"},
 * message = "Un autre utilisateur s'est déjà inscrit avec cette adresse mail")
 */
class User implements UserInterface // UserInterface = gestion de l'utilisateur ( inscription etc )
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message ="Veuillez renseigner un mail valide")
     */

     /**
      * Comparaison du champ ci dessous avec le champ password
      *@Assert\EqualTo(propertyPath="password", message = "Les 2 mots de passe ne correspondent pas.")
      */

    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */

    private $email;

    /**
     * @ORM\Column(type="json")
     * 
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Url()
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,minMessage="Votre Description doit comporter au moins 10 caractères")
     */
    private $Introduction;

    /**
     * @ORM\Column(type="text", nullable= true)
     * @Assert\Length(min=100, minMessage="Votre intro doit comporter au moins 100 caractères")
     */


    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="user")
     */
    private $ads;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;

    private $hash;

    public function getFullName(){
        return "{$this->firstname} {$this->lastname}";
    }

    public function __construct()
    {
        $this->ads = new ArrayCollection();
    }

     /**
     * Creation d'une fonction pour permettre d'initialiser le slug ( avant la persistance et avant la MAJ )
     * 
     * @ORM\PrePersist // Ce qui se passe juste avant d'ajouter une nouvelle donnée
     * @ORM\PreUpdate  // Ce qui passe après avoir ajouter la donnée
     * 
     */

    public function initializeSlug(){
        if(empty($this->slug)){

            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname.' '.$this->lastname);
        }
     }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    /**
     * Get the value of avatar
     */ 
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of avatar
     *
     * @return  self
     */ 
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get the value of Introduction
     */ 
    public function getIntroduction()
    {
        return $this->Introduction;
    }

    /**
     * Set the value of Introduction
     *
     * @return  self
     */ 
    public function setIntroduction($Introduction)
    {
        $this->Introduction = $Introduction;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of slug
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */ 
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setUser($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

}
