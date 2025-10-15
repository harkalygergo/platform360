<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserType::class)
            ->add('billing', BillingType::class)
            // add submit button too
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                    'value' => 'Register'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Set to an array because it's not tied to a single entity
            'data_class' => null,
        ]);
    }
}
