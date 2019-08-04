<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="Customer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 * @ApiResource(
 *  collectionOperations={"GET", "POST"},
 *  itemOperations={"GET", "PUT"={"denormalization_context"={"groups"={"put"}}}, "DELETE"},
 *  subresourceOperations={
 *      "invoices_get_subresource"={"path"="/customers/{id}/invoices"}
 *  },
 * )
 * @ApiFilter(SearchFilter::class, properties={"firstName": "partial"})
 * @ApiFilter(OrderFilter::class)
 */
class Customer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"customers_read", "invoices_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le prénom du customer est obligatoire")
     * @Groups({"put"})
     * @Assert\Length(min=3, minMessage="Le prénom doit faire entre 3 et 255 caractères", max=255, maxMessage="Le prénom doit faire entre 3 et 255 caractères")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom de famille du customer est obligatoire")
     * @Groups({"put"})
     * @Assert\Length(min=3, minMessage="Le nom de famille doit faire entre 3 et 255 caractères", max=255, maxMessage="Le nom de famille doit faire entre 3 et 255 caractères")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'adresse email du customer est obligatoire")
     * @Assert\Email(message="Le format de l'adresse email doit être valide")
     * @Groups({"put"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"put"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Invoice", mappedBy="customer", cascade={"persist","remove"})
     * @ApiSubresource
     */
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="customers", cascade={"persist"})
     * @Assert\NotBlank(message="L'utilisateur est obligatoire")
     */
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    /**
     * Permet de récupérer le total des invoices
     * @Groups({"customers_read"})
     * @return float
     */
    public function getTotalAmount()
    {
        return array_reduce($this->invoices->toArray(), function ($total, $invoice) {
            return $total + $invoice->getAmount();
        }, 0);
    }

    /**
     * Récupérer le montant total non payé (montant total hors factures payées ou annulées)
     * @Groups({"customers_read"})
     * @return float
     */
    public function getUnpaidAmount()
    {
        return array_reduce($this->invoices->toArray(), function ($total, $invoice) {
            return $total + ($invoice->getStatus() === "PAID" || $invoice->getStatus() === "CANCELLED" ? 0 : $invoice->getAmount());
        }, 0);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(string $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice)
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice)
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
