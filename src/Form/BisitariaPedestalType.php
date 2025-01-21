<?php

namespace App\Form;

use App\Entity\Bilera;
use App\Entity\Bisita;
use App\Entity\Bisitaria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BisitariaPedestalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Izena')
            ->add('Abizena')
            ->add('Nondik')
            ->add('Email')
            ->add('Gehitu', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bisitaria::class,
        ]);
    }
}
