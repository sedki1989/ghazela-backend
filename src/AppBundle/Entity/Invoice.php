<?php

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
/**
 * @ORM\Table(name="Invoice")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 * @ApiResource(
 *  itemOperations={"GET", "PUT"={"denormalization_context"={"groups"={"put_invoice"}}}, "DELETE"},
 *  subresourceOperations={
 *      "invoices_get_subresource"={"path"="/customers/{id}/invoices"}
 *  },
 * collectionOperations={
 *     "get"={"method"="GET"},
 *     "post"={"method"="POST"},
 *     "getinvoicesByUser"={
 *         "pagination_enabled"=true,
 *         "normalization_context"={"groups"={"invoices_read"}},
 *         "route_name"="api_users_get_listInvoices",
 *         "hydra_context"={"@type"="hydra:Operation", "hydra:title"="Download operation", "returns"="xmls:string"},
 *         "swagger_context"={
 *              "parameters" = {
 *                     {
 *                         "name" = "id",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "integer"
 *                     }
 *                 },
 *     }}},
 *  attributes={
 *      "order": {"sentAt":"desc"},
 *  },
 *
 * )
 * @ApiFilter(OrderFilter::class, properties={"amount","sentAt"})
 *
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource", "put_invoice"})
     * @Assert\NotBlank(message="Le montant de la facture est obligatoire !")
     * @Assert\Type(type="numeric", message="Le montant de la facture doit être un numérique !")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource", "put_invoice"})
     * @Assert\DateTime(message="La date doit être au format YYYY-MM-DD")
     * @Assert\NotBlank(message="La date d'envoi doit être renseignée")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read", "customers_read", "invoices_subresource", "put_invoice"})
     * @Assert\NotBlank(message="Le statut de la facture est obligatoire")
     * @Assert\Choice(choices={"SENT", "PAID", "CANCELLED"}, message="Le statut doit être SENT, PAID ou CANCELLED")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank(message="Le client de la facture doit être renseigné")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     */
    private $customer;

    /**
     * Permet de récupérer le User à qui appartient finalement la facture
     * @return User
     */
    public function getUser()
    {
        return $this->customer->getUser();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt()
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

}
