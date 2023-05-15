<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Le format doit etre une date")
     * @Assert\GreaterThan("today",message="La date d'arrivée doit etre ultérieur à la date d'aujourd'hui",groups="front")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Le format doit etre une date")
     * @GreaterThan(propertyPath="startDate",message="La date de départ doit etre plus eloignée quela date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    /**
     * Callback appelé à chaque fois qu'on crée une reservation
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return Response
     */

    public function prePersist(){

        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime(); // le slash se refere au fichier principal 
        }

        if(empty($this->amount)){

            // le prix de l'annonce 

            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookable(){

        // Il faut connaitre les dates déjà reservées 
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // Il faut connaitre les dates qui sont en cours de reservation

        $bookingDays = $this->getDays();
        // comparaison 

        $notAvailableDays = array_map(function($day){

            return $day->format('Y-m-d');
        }, $notAvailableDays);

        $days = array_map(function($day){

            return $day->format('Y-m-d');
        }, $bookingDays);

        // On retourne vrai ou faux 
        foreach($days as $day){

            if(array_search($day,$notAvailableDays) !== false)return false;
        }

        return true;
    }
    
    // Calcul du nombre de jours du séjour 

    public function getDays(){

        $resultat = range($this->startDate->getTimestamp(),$this->endDate->getTimestamp(),24*60*60);

        $days = array_map(function($dayTimestamp){

                return new \DateTime(date('Y-m-d', $dayTimestamp));
        },$resultat);

        return $days;

    }

    public function getDuration(){
        
        $difference = $this->endDate->diff($this->startDate);
        return $difference->days;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
