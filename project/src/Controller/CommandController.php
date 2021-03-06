<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandFormType;
use App\Entity\Company;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

#[Route('/command')]
class CommandController extends AbstractController
{
    #[Route('/', name: 'command_index', methods: ['GET'])]
    public function index(CommandRepository $commandRepository): Response
    {
        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
    }

    #[Route('/untreated', name: 'command_untreated', methods: ['GET'])]
    public function untreated(CommandRepository $commandRepository): Response
    {
            return $this->render('command/untreated.html.twig', [
                'commands' => $commandRepository->findBy(
                    ['status' => 'Non-traitée']
                    
                ),
            ]);
    }

    #[Route('/late', name: 'command_late', methods: ['GET'])]
    public function late(CommandRepository $commandRepository): Response
    {
            return $this->render('command/late.html.twig', [
                'commands' => $commandRepository->findBy(
                    ['status' => 'Retard']
                    
                ),
            ]);
    }

    #[Route('/treated', name: 'command_treated', methods: ['GET'])]
    public function treated(CommandRepository $commandRepository): Response
    {
            return $this->render('command/treated.html.twig', [
                'commands' => $commandRepository->findBy(
                    ['status' => 'Traitée']
                    
                ),
            ]);
    }

    #[Route('/paid', name: 'command_paid', methods: ['GET'])]
    public function paid(CommandRepository $commandRepository): Response
    {
            return $this->render('command/paid.html.twig', [
                'commands' => $commandRepository->findBy(
                    ['status' => 'Payée']
                    
                ),
            ]);
    }

    #[Route('/new', name: 'command_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $command = new Command();
        $form = $this->createForm(CommandFormType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command->setStatus('Non-traitée');
            $entityManager->persist($command);
            $entityManager->flush();

            /*return $this->json([
                'status'=> 200,
                'message'=> 'invoice sent'
            ]);

            $html = $this->renderView('facture/facture.html.twig', array(
                'command' => $command,
                'company' => $company,
            ));
            //dd('ok');
            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'file.pdf'
            );
            */

            return $this->redirectToRoute('command_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('command/new.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'command_show', methods: ['GET'])]
    public function show(Command $command, ProductRepository $productRepository): Response
    {
        if ($command->getTotalPaid() == $command->getTotal()) {
            $command->setStatus('Payée');
            $this->getDoctrine()->getManager()->flush();
        }
        if ($command->getDueDate() <  date_default_timezone_get()) {
            $command->setStatus('Retard');
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    #[Route('/{id}/edit', name: 'command_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Command $command, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(CommandFormType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $email = (new Email())
            ->from('hello@example.com')
            ->to($command->getClientMail())
            ->subject('Commande modifiée')
            ->text('Sending emails is fun again!')
            ->html('<p>Commande modifiée !  Voici votre nouvelle facture</p>');

            $mailer->send($email);

            return $this->redirectToRoute('command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('command/edit.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/sendfacture', name: 'send_facture', methods: ['GET', 'POST'])]
    public function sendfacture(Request $request, Command $command, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $command->setStatus('Traitée');
        $this->getDoctrine()->getManager()->flush();

        $email = (new Email())
            ->from('hello@example.com')
            ->to($command->getClientMail())
            ->subject('Commande Traitée')
            ->text('Sending emails is fun again!')
            ->html('<p>Commande Traitée !  Voici votre facture</p>');

            $mailer->send($email);
        return $this->redirectToRoute('command_show', ['id' => $command->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/relancer', name: 'relancer', methods: ['GET', 'POST'])]
    public function relancer(Request $request, Command $command, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($command->getClientMail())
            ->subject('Toc toc toc ! Vous êtes en retard')
            ->text('Sending emails is fun again!')
            ->html('<p>Commande en retard !  Voici votre facture, veuillez la régler svp</p>');

        $mailer->send($email);
        return $this->redirectToRoute('command_show', ['id' => $command->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'command_delete', methods: ['POST'])]
    public function delete(Request $request, Command $command, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
            $entityManager->remove($command);
            $entityManager->flush();
        }

        return $this->redirectToRoute('command_index', [], Response::HTTP_SEE_OTHER);
    }
}
