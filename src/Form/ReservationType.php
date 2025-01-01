<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Immobilier;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('statut')
            ->add('date_res', null, [
                'widget' => 'single_text',
            ])
            ->add('facture', EntityType::class, [
                'class' => Facture::class,
                'choice_label' => 'id',
            ])
            ->add('immobilier', EntityType::class, [
                'class' => Immobilier::class,
                'choice_label' => 'id',
            ])
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
