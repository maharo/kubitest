<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = $this->createCategories($manager, [
            'Citadine',
            'Compacte',
            'Sport',
            'SUV',
            'Grosse voiture'
        ]);

        $brands = $this->createBrandsAndSuppliers($manager, $faker, [
            'Peugeot',
            'Renault',
            'Citroen',
            'BMW',
            'Mercedes'
        ]);

        $this->createProducts($manager, $faker, $categories, $brands, 20);

        $this->createUsers($manager, $faker);

        $manager->flush();
    }

    private function createCategories(ObjectManager $manager, array $categoryNames): array
    {
        $categories = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    private function createBrandsAndSuppliers(ObjectManager $manager, \Faker\Generator $faker, array $brandNames): array
    {
        $brands = [];
        foreach ($brandNames as $name) {
            $brand = new Brand();
            $brand->setName($name);

            $manager->persist($brand);
            $brands[] = $brand;

            $supplier = new Supplier();
            $supplier->setName('Fournisseur ' . $name)
                ->setEmail($faker->email)
                ->setPhone($faker->phoneNumber)
                ->setBrand($brand);

            $manager->persist($supplier);
        }

        return $brands;
    }

    private function createProducts(ObjectManager $manager, \Faker\Generator $faker, array $categories, array $brands, int $count): void {
        for ($i = 0; $i < $count; $i++) {         
            $product = new Product();
            $product->setName($faker->randomElement($brands)->getName() . ' ' . $faker->word)
                ->setDescription($faker->sentence)
                ->setPrice($faker->randomFloat(2, 5000, 100000))
                ->setCategory($faker->randomElement($categories))
                ->setBrand($faker->randomElement($brands))
                ->setStock($faker->randomDigit(20))
                ->setYear($faker->numberBetween(1900, date('Y')))
                ->setEnergy(array_rand(array_combine(Product::ENERGIES, Product::ENERGIES)))
            ;

            $manager->persist($product);
        }
    }

    private function createUsers(ObjectManager $manager, $faker): void
    {
        $admin = new User();
        $admin->setEmail('admin@mail.com')
            ->setLastName('Admin')
            ->setFirstName('Kubi')
            ->setRoles(['ROLE_ADMIN'])
            ->setAddress($faker->address)
            ->setPassword(
            $this->userPasswordHasher->hashPassword(
                $admin,
                'admin'
            )
        );

        $user = new User();

        $user->setEmail('user@mail.com')
        ->setLastName('User')
            ->setFirstName('Kubi')
            ->setRoles(['ROLE_USER'])
            ->setAddress($faker->address)
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    'user'
                )
            );
        $manager->persist($admin);
        $manager->persist($user);

    }
}
