<?php

namespace App\Controller;


use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;


class MainController extends AbstractController
{
    #[Route('', name: 'index')]
    // tout ce qui est racine va executer la fonction index()
    public function index(CommandeRepository $repository): Response
    // Entité Manager Interface : permet de faire des requêtes sur la base de données
    {/* 
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
         */
        $commandes = $repository->findAll();
        return $this->render('main/index.html.twig', [
            'commandes' => $commandes,
        ]);

    }

    #[Route('/{id}', name:'show', requirements: [
        'id' => Requirement :: DIGITS 
    ])]
    
    // Affiche la page de détail d'une commande selon son id
    public function show(Commande $commande): Response
    {
        return $this->render('main/show.html.twig', [
            'commande' => $commande,
        ]);
    }


}
