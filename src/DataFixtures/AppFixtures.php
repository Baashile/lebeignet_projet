<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\ElementCommande;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        // hashage de mot de passe en tenant compte de l'algorithm "auto" précisé dans la configuration dossier/package/security implémenté dans la classe user 
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Charger les clients d'abord pour pouvoir les référencer dans les commandes
        $this->loadClients($manager);
        // Charger les produits avant les commandes pour les référencer dans les éléments de commande
        $this->loadProduits($manager);
        // Charger les commandes ensuite en utilisant les références des clients
        $this->loadCommandes($manager);
        // Charger les éléments de commande en utilisant les références des produits et des commandes
        $this->loadElementsCommandes($manager);
        // Charger les utilisateurs à la fin, cela n'a pas d'impact sur les autres entités
        $this->loadUsers($manager);

        // Écriture dans la base de données
        $manager->flush();
    }

    // Entity : Client
    private function loadClients(ObjectManager $manager): void
    {
        foreach ($this->getClientsData() as [$nom, $prenom, $adresse, $numeroTelephone, $email]) {
            $client = (new Client())
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setAdresse($adresse)
                ->setNumeroTelephone($numeroTelephone)
                ->setEmail($email);

            $manager->persist($client);
            $this->addReference('client_' . $nom, $client);
        }
        $manager->flush();
    }

    // get information
    private function getClientsData(): array
    {
        return [
            // $clientsData = [$nom, $prenom, $adresse, $numeroTelephone, $email]
            ['Dupont', 'Jean', '12 rue de la République', '0612345678', 'dupont1@email.com'],
            ['Du', 'Ean', '15 rue de la République', '0698765432', 'du2@email.com'],
            ['Pont', 'Der', '18 rue de la République', '0634567890', 'pont3@email.com'],
            ['Martin', 'Paul', '20 rue de la République', '0656789012', 'martin4@email.com'],
            ['Grande', 'Grande', '22 rue de la République', '0644444444', 'thompson9@email.com'],
            ['Jackson', 'David', '24 rue de la République', '0655555555', 'jackson10@email.com'],
            ['Lee', 'David', '22 rue de la République', '0678901234', 'lee5@email.com'],
            ['Brown', 'Sarah', '24 rue de la République', '0611111111', 'brown6@email.com'],
            ['Johnson', 'Michael', '26 rue de la République', '0622222222', 'johnson7@email.com'],
            ['Williams', 'Christopher', '28 rue de la République', '0633333333', 'williams8@email.com'],
            ['Bron', 'Andrew', '30 rue de la République ', '0622222222', ' johnson7@email.com'], 
        ];
    }

    // Entity : Produit
    private function loadProduits(ObjectManager $manager): void
    {
        // création d'un objet Produit
        foreach ($this->getProduitsData() as [$nom, $description, $prix, $quantiteStock]) {
            $produit = (new Produit())
                ->setNom($nom)
                ->setDescription($description)
                ->setPrix($prix)
                ->setQuantiteStock($quantiteStock);

            $manager->persist($produit);
            $this->addReference('produit_' . $nom, $produit); // Assurez-vous que la référence est correctement définie
        }
        // équivalent à commit
        $manager->flush();
    }

    private function getProduitsData(): array
    {
        // création d'un objet Produit
        return [
            // $produitsData = [$nom, $description, $prix, $quantiteStock]
            ['Citron', 'Citron vert', 10, 7],
            ['Pomme', 'Pomme rouge', 15, 7],
            ['Poire', 'Poire verte', 20, 7],
        ];
    }


    private function loadCommandes(ObjectManager $manager): void
    {
        foreach ($this->getCommandesData() as [$dateCommande, $etat, $montantTotal, $statutPaiement, $methodeLivraison, $clientNom, $description, $archived]) {
            $client = $this->getReference('client_' . $clientNom);
    
            $commande = (new Commande())
                ->setDateCommande($dateCommande)
                ->setEtat($etat)
                ->setMontantTotal($montantTotal)
                ->setStatutPaiement($statutPaiement)
                ->setMethodeLivraison($methodeLivraison)
                ->setClient($client)
                ->setDescription($description)
                ->setArchived($archived); // Ajout de l'archivage
    
            $manager->persist($commande);
            $this->addReference('commande_' . $dateCommande->format('YmdHis'), $commande);
        }
        $manager->flush();
    }

    private function getCommandesData(): array
    {
        return [
            [new \DateTimeImmutable('2022-01-01'), 'en cours', 10, 'payé', 'Livraison', 'Dupont', 'Commande 100 beignets  1 box découverte + 50 beignets natures', false],
            [new \DateTimeImmutable('2022-01-02'), 'validé', 15, 'payé', 'Livraison', 'Du', '50 beignets ⇒ 20 cannelle, 20 sucre, 10 beurre de cacahuète', true],
            [new \DateTimeImmutable('2022-01-03'), 'en cours', 20, 'non payé', 'Livraison', 'Pont', '50 beignets natures + livraison', false],
            [new \DateTimeImmutable('2022-01-04'), 'en cours', 25, 'payé', 'En magasin', 'Martin', 'Commande 250 beignets natures + 100 beignets natures + 100 beignets rouges', false],
            [new \DateTimeImmutable('2022-01-06'), 'en cours', 40, 'payé', 'Livraison', 'Brown', '100 beignets rouges + 50 beignets natures + 100 beignets rouges', false],
            [new \DateTimeImmutable('2022-01-08'), 'en cours', 50, 'payé', 'Livraison', 'Johnson', 'Commande 500 beignets rouges + 500 beignets natures + 500 beignets rouges', false],
            
        ];
    }

    private function loadElementsCommandes(ObjectManager $manager): void
    {
        // création d'un objet ElementCommande
        // destructuration , chaque index va être associés à la variable qui correspondant dans la boucle
        foreach ($this->getElementData() as [$quantite, $prixUnitaire, $prixTotal, $produitNom]) {
            // Récupérez le produit correspondant au nom fourni
            $produit = $this->getReference('produit_' . $produitNom);
    
            $elementCommande = (new ElementCommande())
                ->setQuantite($quantite)
                ->setPrixUnitaire($prixUnitaire)
                ->setPrixTotal($prixTotal)
                ->setProduit($produit); // Assurez-vous que le produit est bien défini
    
            $manager->persist($elementCommande);
            
            // Utilisez une clé unique pour chaque référence
            $referenceKey = 'element_' . $produitNom . '_' . $quantite;
            $this->setReference($referenceKey, $elementCommande);
        }
        // equivalent a commit
        $manager->flush();
    }

    private function getElementData(): array
    {
        // création d'un objet ElementCommande
        return [
            // $elementData = [$quantite, $prixUnitaire, $prixTotal, $produitNom, $commandeDate]
            [2, 1.5, 3.0, 'Citron', new \DateTimeImmutable('2022-01-01')],
            [2, 1.5, 3.0, 'Pomme', new \DateTimeImmutable('2022-01-02')],
            [2, 1.5, 3.0, 'Poire', new \DateTimeImmutable('2022-01-03')],
        ];
    }

    // Entity : User
    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$nom, $prenom, $email, $motDePasse, $roles]) {
            $user = (new User())
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setEmail($email)
                ->setPassword($motDePasse)
                ->setRoles($roles);
            $user->setPassword($this->hasher->hashPassword($user, $motDePasse));

            $manager->persist($user);
            $this->addReference('user_' . $nom, $user);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$nom, $prenom, $email, $motDePasse, $role]
            ['Dupont', 'Jean', 'jean.dupont@email.com', 'nodm', ["ROLE_ADMIN"]],
            ['Du', 'Ean', 'ean.du@email.com', 'nosm', ["ROLE_USER"]],
            ['Pont', 'Der', 'der.pont@email.com', 'wfm', ["ROLE_USER"]],
            ['Marin', 'Saul', 'saul.martin@email.com', 'nddfsom', ["ROLE_USER"]],
            ['See', 'wid', 'wvid.see@email.com', 'ndasfom', ["ROLE_USER"]],
            ['Brown', 'Sarah','sarah.brown@email.com', 'nfdsadom', ["ROLE_USER"]],
        ];
    }

    /* 
    php bin/console doctrine:fixtures:load
    ------------------migration----------------------
    php bin/console do:mi:mi
    ------------------Looad fictures ----------------------
    php bin/console do:fi:lo
    */
}
