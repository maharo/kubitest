<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class   RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'email', 
            EmailType::class, 
            [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Email ne peut pas être vide.',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez saisir un adresse email correcte.',
                    ]),
                ],
            ]
        )
        ->add(
            'firstName', 
            TextType::class, 
            [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le prenom doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Veuillez entrer votre prenom',
                    'label' => 'Prenom'
                ]
        ])
        ->add(
            'lastName', 
            TextType::class, 
            [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Veuillez entrer votre nom'
                ]
        ])
        ->add(
            'phone', 
            TelType::class, 
            [
                'label' => 'Téléphone',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le numero de telephone ne peut pas être vide.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\+?[0-9]{7,15}$/',
                        'message' => 'Veuillez entrer un numero valide',
                ]),
            ],
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrer un numero de telephone (e.g., +123456789)'
            ]
        ])
        ->add(
            'address',
            TextType::class,
            [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'adresse ne peut pas être vide.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter l\'adresse'
                ]
            ]
        )
        ->add(
            'agreeTerms', 
            CheckboxType::class, [
                'label' => 'Termes et conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions',
                    ]),
                ],
        ])
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
