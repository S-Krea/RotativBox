<?php

namespace App\Form\Admin;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maintenanceCost', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix Maintenance',
                'help' => 'Ce montant est rajouté <ul><li>x3 sur les box 6 items</li> <li>x4 sur les box 9 items</li></ul>',
                'help_html' => true,
            ])
            ->add('dacOptionPrice', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix option DAC',
                'help' => 'Ce montant est ajouté au total du panier avant calcul des mensualités.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
