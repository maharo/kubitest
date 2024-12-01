<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'required' => false,
                    'data' => $this->getPrefilledValues('name')
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'label' => 'Type',
                    'class' => Category::class,
                    'required' => false,
                    'data' => $this->getPrefilledValues('category')  
                ]
            )
            ->add(
                'brand',
                EntityType::class,
                [
                    'label' => 'Marque',
                    'class' => Brand::class,
                    'required' => false,
                    'data' => $this->getPrefilledValues('brand')
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => '<i class="fa fa-search"></i>',
                'label_html' => true,
                'attr' => ['class' => 'btn btn-primary'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    private function getPrefilledValues(string $key)
    {
        $prefilledValues = $this->session->get('filter', []);

        if (!array_key_exists($key, $prefilledValues)) {
            return null;
        }

        if(!$prefilledValues[$key]) {
            return null;
        }

        $repositories = [
            'category' => Category::class,
            'brand' => Brand::class,
        ];

     

        if (isset($repositories[$key])) {
            return $this->entityManager->getRepository($repositories[$key])->find($prefilledValues[$key]);
        }

        return $prefilledValues[$key];
    }

}
