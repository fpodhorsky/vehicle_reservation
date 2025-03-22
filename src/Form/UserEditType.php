<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Jméno',
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'E-mail',
            ])
            ->add('canReserve', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    'Ano' => true,
                    'Ne' => false
                ],
                'label' => 'Může rezervovat vozidla?',
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control form-check roles'
                ],
                'choices' => [
                    'Administrátor' => "ROLE_ADMIN",
                    'Běžný uživatel' => "ROLE_USER"
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Role',
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /* @var $user User */
            $user = $event->getData();
            $form = $event->getForm();

            if ($user->getId() === null) {
                $label = "Přidat uživatele";
            } else {
                $label = "Uložit změny";
            }

            $form->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right mt-3'
                ],
                'label' => $label
            ]);

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
