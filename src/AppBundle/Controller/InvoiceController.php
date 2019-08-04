<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InvoiceController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @Route(
     *     name="api_users_get_listInvoices",
     *     path="api/users/{id}/invoices",
     *     requirements={"id" = "\d+"},
     *     defaults={
     *         "_api_resource_class"=Invoice::class,
     *         "_api_collection_operation_name"="getinvoicesByUser"
     *     }
     * )
     */
    public function __invoke(Request $request,$id)
    {
        $page = $request->query->get('page', 1);
        $pageSize = $request->query->get('pageSize', 1);
        $invoices = $this->getDoctrine()
            ->getRepository(Invoice::class)
            ->findInvoicesByUserId($id,$page,$pageSize);

        return $invoices;
    }
}
