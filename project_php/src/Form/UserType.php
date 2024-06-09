<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Saisir un prénom']),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Saisir un email']),
                    new Email(['message' => 'Veuillez saisir un email valide']),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank(['message' => 'Saisir un mot de passe']),
                        new Length(['min' => 8, 'minMessage' => 'Votre mot de passe doit avoir une longueur minimale de {{ limit }} caractères.']),
                        new Regex([
                            'pattern' => '/^(?=.[A-Za-z])(?=.\d)/',
                            'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
                        ]),
                    ]
                ],
                'second_options' => [
                    'constraints' => [
                        new NotBlank(['message' => 'Confirmer votre mot de passe']),
                    ]
                ],
                'invalid_message' => 'Les mots de passe doivent être les mêmes.',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true
        ]);
    }
}
