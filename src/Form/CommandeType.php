<?php

// src/Form/CommandeType.php

namespace App\Form;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                ],
                'required' => false,
            ])
            ->add('montantTotal', NumberType::class, [
                'required' => false,
            ])
            ->add('statutPaiement', ChoiceType::class, [
                'choices' => [
                    'Non payé' => 'Non payé',
                    'Payé' => 'Payé',
                ],
                'required' => false,
            ])
            ->add('methodeLivraison', TextType::class, [
                'required' => false,
            ])
            ->add('dateCommande', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'html5' => true,
            ])
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getNom() . ' ' . $client->getPrenom();
                },
                'required' => true,
                'placeholder' => 'Sélectionner un client existant',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
