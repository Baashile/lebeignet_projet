<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Security\Voter\CommandeVoter;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




#[Route('/admin/webapp', name: 'admin_webapp_')]
final class WebAppController extends AbstractController
{
    // ::::: gestion du CRUD + Valadation dufomulaire garantir que les données sont correctement saisies + Sécurisation avec gestion des rôles :::::::
    // CRUD : LECTURE

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CommandeRepository $repository): Response
    {
        //$Commandes = $repository->findAll();
        // Je récupère toutes les commandes qui ne sont pas archivées (archived = false)
        // ::::: Pour des raisons de sécurité, il est préférable de ne pas récupérer toutes les commandes de la base de données à la fois, car cela peut prendre du temps et potentiellement causer des problèmes de performances si vous avez beaucoup de données.
        // ::::: En revanche, il est possible de récupérer les commandes archivées à la volée à l'aide d'une requête spécifique.
        // ---- corr --- pour récupérer uniquement les commandes non archivées, on utilise le findby() avec une condition sur le champ archived (ici, on utilise false)
        $Commandes = $repository->findActived();
        return $this->render(
            'admin/webapp/index.html.twig',
            [
                'Commandes' => $Commandes,
            ]
        );
    }

// CRUD : CREATION
// Get pour afficher les formulaires et POST pour recevoir les données quand le formulaire sera soumis
// ---- corr --- gestion unique d'affichage des commande ; cette page se cjargeuniquement de l'affichage 
#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
public function create(Request $request, EntityManagerInterface $em): Response
{
    $commande = new Commande();
    /* Le formulaire est créé à partir de la classe CommandeType et associé à l'objet $commande.
handleRequest() gère automatiquement la soumission du formulaire et met à jour l'objet $commande avec les données soumises, si le formulaire a été envoyé.
 */
    $form = $this->createForm(CommandeType::class, $commande);

    $form->handleRequest($request); // gère automatiquement la soumission du formulaire

    return $this->json(['submit' => $form->isSubmitted(),'valid'=>$form->isValid()]);


    if ($form->isSubmitted() && $form->isValid()) {
/* Récupération du client existant ou du nouveau clientSi le formulaire est soumis et valide, le code récupère les données du champ client_existant.
Si un client existant est sélectionné, il est associé à la commande.
Si aucun client n'est sélectionné, un message d'erreur est affiché, et le formulaire est renvoyé à l'utilisateur pour qu'il puisse corriger sa sélection. */
        // Gestion des clients existants ou nouveaux
        $clientExistant = $form->get('client_existant')->getData();

        return $this->json($clientExistant);
        // $nouveauClientNom = $form->get('nouveau_client_nom')->getData();

        if ($clientExistant) {
            $commande->setClient($clientExistant);
        }else {
            $this->addFlash('error', 'Veuillez sélectionner un client existant ou en ajouter un nouveau.');
            return $this->render('admin/webapp/new.html.twig', [
                'form' => $form->createView(),
                'Commande' => $commande
            ]);
            
        }

        // Sauvegarde en base de données
        $em->persist($commande);
        $em->flush();

        $this->addFlash('success', 'La commande a bien été créée');

        return $this->redirectToRoute('admin_webapp_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('admin/webapp/new.html.twig', [
        'form' => $form->createView(),
        'Commande' => $commande
    ]);
}

    // CRUD : MODIFICATION Edition de données
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        // ::::: Acces refusé si on est pas user ::::::
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // Vérifie si le Commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Le Commande n\'existe pas');
        }
        // le même formulaire pour la création et édition
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde en base de données
            $em->flush();

            $this->addFlash('success', 'Le Commande a bien été modifié');

            // redirection
            return $this->redirectToRoute('admin_webapp_show', [
                'id' => $commande->getId()

            ], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(
            'admin/webapp/edit.html.twig',
            [

                'form' => $form, 'Commande' => $commande
            ]
        );
    }


    // ::::: gestion des archives :::::::
    #[Route('/archives', name: 'archives', methods: ['GET'])]
    public function archives(CommandeRepository $repository): Response
    {
        $CommandesArchivees = $repository->findArchived();
    
        return $this->render(
            'admin/webapp/archives.html.twig',
            [
                'CommandesArchivees' => $CommandesArchivees,
            ]
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render(
            'admin/webapp/show.html.twig',
            [
                'Commande' => $commande,
            ]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]

    public function deleted(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        // Acces refusé si on est pas user 
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var string |null $token */
        $token = $request->getPayload()->get('token');

        // vérifier si le token est valide, l'article est supprimer uniquement si le token est valide.
        if ($this->isCsrfTokenValid('delete', $token)) {
            // Suppression de la commande
            $em->remove($commande);
            $em->flush();
        }


        $this->addFlash('success', 'Le Commande a bien été supprimé');

        // Redirection vers la liste des commandes
        return $this->redirectToRoute('admin_webapp_index', status: Response::HTTP_SEE_OTHER);
    }

    
}
