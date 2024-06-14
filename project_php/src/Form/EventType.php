<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'évènement'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('datetime_start', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de début de l\'évènement',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de début ne peut pas être dans le passé.',
                    ])
                ]
            ])
            ->add('datetime_end', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de fin de l\'évènement',
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[datetime_start].data',
                        'message' => 'La date de fin doit être après la date de début.',
                    ])
                ]
            ])
            ->add('participant_count', IntegerType::class, [
                'label' => 'Nombre de participants'
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'label' => 'Coût de participation (€)',
                'attr' => ['placeholder' => 'Laissez vide pour un événement gratuit']
            ])
            ->add('is_public', CheckboxType::class, [
                'label' => 'Événement public',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
