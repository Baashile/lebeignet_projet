<?php

// src/Controller/CommandeController.php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commandes', name: 'app_commandes')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(CommandeRepository $commandeRepository): Response
    {

        $commandes = $commandeRepository->findAllWithClientInfo();

        return $this->render('main/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    // -- coorr ; faire une route qui récupère le formulaire ; cete page se charge uniquement du CRUD


    #[Route('/admin/commandes/archive/{id}', name: 'app_commandes_archive', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function archive(int $id, Request $request, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            return new JsonResponse(['status' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('archive' . $commande->getId(), $request->request->get('_token'))) {
            $commande->setArchived(true);
            $entityManager->flush();

            return new JsonResponse(['status' => 'La commande a bien été archivée'], Response::HTTP_OK);
        }

        return new JsonResponse(['status' => 'Action non autorisée'], Response::HTTP_FORBIDDEN);
    }

    #[Route('/commandes/{id}', name: 'app_commandes_show')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(int $id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        return $this->render('main/show.html.twig', [
            'commande' => $commande,
        ]);
    }


    #[Route('/admin/commandes/new', name: 'app_commandes_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        
        // Créer une nouvelle commande
        $commande = new Commande();
        // Création du formulaire
        $form = $this->createForm(CommandeType::class, $commande);
        // Traiter la requête du formulaire
        $form->handleRequest($request);


        // return $this->json(['submit' => $form->isSubmitted(),'valid'=>$form->isValid()]);

        // Si le formulaire est soumis et valide, persister la commande et sauvegarder en base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();
            

            $this->addFlash('success', 'Commande créée avec succès.');
            return $this->redirectToRoute('app_commandes');
        }

        return $this->render('admin/webapp/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/commandes/edit/{id}', name: 'app_commandes_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(int $id, Request $request, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La commande a bien été modifiée');
            return $this->redirectToRoute('app_commandes');
        }

        return $this->render('admin/webapp/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    // Méthode pour supprimer une commande
    #[Route('/admin/commandes/delete/{id}', name: 'app_commandes_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(int $id, Request $request, CommandeRepository $commandeRepository, EntityManagerInterface $entityManager): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
            $this->addFlash('success', 'La commande a bien été supprimée');
        }

        return $this->redirectToRoute('app_commandes');
    }
}
