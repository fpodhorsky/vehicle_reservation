<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicle', EntityType::class, [
                'attr' => [
                    'class' => 'form-control custom-select',
                ],
                'choice_label' => 'note',
                'label' => 'Vyberte auto',

                'class' => Vehicle::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('vehicle')
                        ->where('vehicle.isDeactivated = 0');
                },
            ])
            ->add('reservation_date_from', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Rezervace od',
                'mapped' => false
            ])
            ->add('reservation_date_to', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Rezervace do',
                'mapped' => false
            ])
            ->add('note', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Např.: Školení v Novém Jičíně..'
                ],
                'label' => 'Poznámka k rezervaci'
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /* @var $reservation Reservation */
            $reservation = $event->getData();
            $form = $event->getForm();


            if ($reservation->getId() === null) {
                $label = "Vytvořit rezervaci";
            } else {
                $label = "Uložit změny";
            }

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
            'data_class' => Reservation::class,
        ]);
    }
}
