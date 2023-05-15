<?php

namespace App\Entity; // voir le namespace comme un marker sur une map qui te permet de te servir comme un raccourci


// un use est un require 
use App\Repository\AdRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"title"},
 * message="Une autre annonce a déjà ce titre, veuillez en changer"
 * )

 */
class Ad implements \ArrayAccess
{

    private $container;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,max=255,minMessage="Le titre doit faire plus de 10 caracteres",maxMessage= "Votre titre est trop long, pas plus de 255 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */


    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, minMessage="Merci de mettre au moins 10 caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=100, minMessage="Merci de mettre au moins 100 caractères")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true)
     */
    private $images;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ads")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="ad", orphanRemoval=true)
     */
    private $comments;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
            $this->slug = $slugify->slugify($this->title);

        }
     }
    

    /**
     * Permet de récuperer le commenteaire d'un auteur par rapport à une annonce
     * 
     * @param User $author
     * @return Comment | null è
     */


    public function getCommentFromAuthor(User $author){

        foreach($this->comments as $comment){

            if($comment->getUser() === $author ) return $comment;

        }

        return null;
    }

    public function getAverageRatings(){

        // Calcul dela somme des notes

        $sum = array_reduce($this->comments->toArray(),function($total,$comment){

            // On retourne le total + la note de chaque commentaire 

            return $total + $comment->getRating();
        },0);

        // diviser le total par le nombre de notes

        if (count($this->comments) > 0 ) return $sum / count($this->comments);
        return 0;
    }


    public function getNotAvailableDays(){

        $notAvailableDays = [];

        foreach($this->bookings as $booking){

            // $resultat = range(10,20,2); =>[10,12,14,16,18,20]
            // $resultat = range(03-20-2019,03-25-2019) =>[]

            $resultat = range(
                             $booking-> getStartdate()->getTimeStamp(),
                             $booking->getEndDate()->getTimestamp(),
                             24 * 60 * 60
            );

            $days = array_map(function($dayTimestamp){

                return new \Datetime(date('Y-m-d',$dayTimestamp));
            },$resultat);

            $notAvailableDays = array_merge($notAvailableDays,$days);
        }

        return $notAvailableDays;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /*
    * No error
    */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /*
    * No error
    */
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    /*
    * No error
    */
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }

}
