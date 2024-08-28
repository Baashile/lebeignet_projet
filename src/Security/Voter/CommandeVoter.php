<?php

namespace App\Security\Voter;

use App\Entity\Commande;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommandeVoter extends Voter
{
    public const MODIF = 'COMMANDE_MODIF';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Vérifiez si l'attribut est celui attendu et si le sujet est une commande
        return in_array($attribute, [self::MODIF]) && $subject instanceof Commande;
    }

    /**
     * @param Commande $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Récupérez l'utilisateur connecté
        $user = $token->getUser();

        // Si l'utilisateur est anonyme, ne pas accorder l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifiez les rôles de l'utilisateur pour l'attribut MODIF
        if ($attribute === self::MODIF) {
            // Vérifiez si l'utilisateur a le rôle d'admin
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return true;
            }
        }

        return false;
    }
}
