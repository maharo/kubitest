<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Supplier;
use App\Repository\BrandRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SupplierType extends AbstractType
{
    private $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'constraints' => [
                        new Assert\Length([
                            'min' => 3,
                            'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        ]),
                    ],
                ]
            )
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
                        ])
                    ]
                ]
            )
            ->add(
                'brand', 
                EntityType::class, 
                [
                    'label' => 'Marque',
                    'class' => Brand::class,
                    'choices' => $options['is_new']
                        ? $this->brandRepository->findBrandsWithoutSupplier()
                        : [$options['current_brand']],
                    'choice_label' => 'name',
                    'placeholder' => 'Select a brand',
                    'attr' => [
                        'required' => true
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,'is_new' => false,
            'current_brand' => null,
        ]);
    }
}
