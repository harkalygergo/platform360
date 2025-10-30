<?php

namespace App\Form\Platform;

use App\Entity\Platform\Task\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true, 'label' => 'Title'])
            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Description'])
            ->add('deadline', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Deadline'
            ])
            ->add('priority', IntegerType::class, ['required' => false, 'label' => 'Priority'])
            ->add('status', CheckboxType::class, ['required' => false, 'label' => 'Completed'])
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
