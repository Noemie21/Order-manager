<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandFormType;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    #[Route('/{id}/edit', name: 'command_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Command $command, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandFormType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('command/edit.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
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
