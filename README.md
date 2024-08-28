# lebeignet_projet
rendu de travaille de bachelor application métier pour prise de commande pour l'entreprise LeBeignet C'est Bon !
# Guide de Déploiement de l'Application

## Prérequis

Pour lancer cette application, vous devez vous connecter à une machine virtuelle qui héberge l'application.

## Instructions pour lancer l'application

1. **Connexion à la machine virtuelle :**

   - Assurez-vous d'être connecté à la machine virtuelle où l'application est hébergée.

2. **Lancer l'application :**

   - Démarrez l'application sur la machine virtuelle si ce n'est pas déjà fait. Vous pouvez utiliser la commande appropriée selon l'environnement (par exemple, `symfony server:start`, `php -S localhost:<numéro du port> -t public`, etc.).

3. **Ouvrir un navigateur web :**

   - Sur votre navigateur, tapez l'adresse suivante pour accéder à l'application :  

     ```
     localhost:<numéro de votre port>/login
     ```

   - Remplacez `<numéro de votre port>` par le numéro de port configuré pour votre application.

## Connexion à l'application

- **Pour vous connecter à l'application :**
  - Vous disposez de plusieurs utilisateurs préconfigurés dans les DataFixtures **src/DataFixtures** :
    - **Administrateur (admin)**
    - **Utilisateur (user)**

- Utilisez les identifiants de connexion fournis pour vous connecter avec le rôle souhaité.
