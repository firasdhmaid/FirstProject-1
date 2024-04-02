<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Form\PartieType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PartieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partie')]
class PartieController extends AbstractController
{
    #[Route('/', name: 'app_partie_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $parties = $entityManager
            ->getRepository(Partie::class)
            ->findAll();

        return $this->render('partie/index.html.twig', [
            'parties' => $parties,
        ]);
    }

    #[Route('/new/{id}', name: 'app_partie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partie = new Partie();
        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partie);
            $entityManager->flush();

            return $this->redirectToRoute('app_partie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partie/new.html.twig', [
            'partie' => $partie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partie_show', methods: ['GET'])]
    public function show(PartieRepository $partieRepository,Request $request,EntityManagerInterface $entityManager): Response
    {   
        $idTournoi = $request->get('id');
        $parties = $partieRepository->findByIdTournoi($idTournoi);
    
        return $this->render('partie/index.html.twig', [
            'idtournoi'=>$idTournoi,
            'parties' => $parties,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_partie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partie $partie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartieType::class, $partie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_partie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('partie/edit.html.twig', [
            'partie' => $partie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partie_delete', methods: ['POST'])]
    public function delete(Request $request, Partie $partie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($partie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_partie_index', [], Response::HTTP_SEE_OTHER);
    }
}
