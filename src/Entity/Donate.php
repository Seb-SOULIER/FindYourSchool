<?php

namespace App\Entity;

use App\Repository\DonateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DonateRepository::class)
 */
class Donate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "Email non valide")
     */
    private $mail;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(message = "Montant incorrect")
     * @Assert\Range(min = 1, minMessage="Montant minimum 1 €uro")
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

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
}
