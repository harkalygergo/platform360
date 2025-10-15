<?php

namespace App\Form;

use App\Entity\Platform\BillingProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('country', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('zip', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('settlement', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('vat', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('euVat', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingProfile::class,
        ]);
    }
}
