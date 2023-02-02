<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DevisForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbMois', HiddenType::class)
            ->add('financement', HiddenType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('email', EmailType::class)
            ->add('tel', TextType::class, ['label' => 'Téléphone'])
        ;
    }

    public function getBlockPrefix()
    {
        return 'devis';
    }
}