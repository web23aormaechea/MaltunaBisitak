<?php

namespace App\Form;

use App\Entity\Langilea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class LangileaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Izena')
            ->add('Abizena')
            ->add('Telefonoa')
            ->add('Nondik')
            ->add('Giltza', CheckboxType::class, [
                'label'    => 'Giltza uzten da?', // Etiqueta del checkbox
                'required' => false, // Indica si el checkbox es obligatorio
            ])
            ->add('Sortu', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Langilea::class,
        ]);
    }
}
