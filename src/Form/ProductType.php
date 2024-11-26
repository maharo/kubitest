<?php
// src/Form/ProductType.php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Brand;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class, 
                [
                    'label' => 'Nom',
                    'required' => true,
                ]
            )
            ->add(
                'description', 
                TextareaType::class, 
                [
                    'label' => 'Description',
                    'required' => false,
                    'attr' => ['rows' => 5],
                ]
            )
            ->add(
                'price', 
                NumberType::class, 
                [
                    'label' => 'Prix',
                    'required' => true,
                    'scale' => 2,
                ]
            )
             ->add(
                'category', 
                EntityType::class, 
                [
                    'label' => 'Type',
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'required' => true,
                ]
            )
            ->add(
                'brand', 
                EntityType::class, 
                [
                    'label' => 'Marque',
                    'class' => Brand::class,
                    'choice_label' => 'name',
                    'required' => true,
                ]
            )
            
            ->add(
                'year', 
                IntegerType::class, 
                [
                    'label' => 'Année',
                    'required' => true,
                    'attr' => ['min' => 1900, 'max' => date('Y')],
                ]
            )
            ->add(
                'energy',
                ChoiceType::class, 
                [
                    'label' => 'Energie',
                    'choices' => array_combine(Product::ENERGIES, Product::ENERGIES),
                    'required' => true,
                ]
            )
            ->add(
                'stock',
                IntegerType::class,
                [
                    'label' => 'Stock',
                    'required' => true,
                    'attr' => ['min' => 0],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
