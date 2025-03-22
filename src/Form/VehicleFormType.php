<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('spz', TextType::class, options: [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => 6,
                    'maxlength' => 7,
                    'placeholder' => '1T23456'
                ],
                'label' => 'SPZ auta'
            ])
            ->add('note', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bílé auto značky BMW...',
                    'novalidate' => 'novalidate'
                ],
                'label' => 'Popis vozidla',
                'help' => 'Popis bude zobrazen při výběru vozidla  při rezervaci'
            ])
            ->add('isDeactivated', ChoiceType::class, [
                'label' => 'Smí být používáno pro rezervace',
                'choices' => [
                    'Ano' => false,
                    'Ne' => true,
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $car = $event->getData();
            $form = $event->getForm();

            if ($car->getId() === null)
                $label = "Přidat vozidlo";
            else
                $label = "Uložit změny";

            $form->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success float-right mt-3',
                ],
                'label' => $label,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
