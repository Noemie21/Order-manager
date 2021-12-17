<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    #[Route('/test/{id}', name: 'other', methods: ['GET'])]
    public function test(\Knp\Snappy\Pdf $knpSnappyPdf, Command $command, Company $company, )
    {
        $html = $this->renderView('facture/facture.html.twig', array(
            'command' => $command,
            'company' => $company,
        ));
        //dd('ok');
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }
}
