<?php

namespace App\Entity;

use App\Repository\PasswordUpdateRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Response;

class PasswordUpdate
{

    private $oldPassword;

    /**
     * 
     * @Assert\Length(min=8,minMessage="Le mot de passe doit comporter au moins 8 cractères ")
     */

    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword",message="les 2 mots de passe ne correspont pas ")
     * 
     * 
     */
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(?string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

   

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
