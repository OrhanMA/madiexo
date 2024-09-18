<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\ContactRequest;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Entity\OrderItems;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCities($manager);
        $this->loadCategories($manager);
        $this->loadProducts($manager);
        $this->loadUsers($manager);
        $this->contactRequests($manager);
        $this->loadDeliveries($manager);
        $this->loadOrders($manager);
    }

    public function loadCategories(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $category = new Category();
            $category->setName("Category #$i");
            $manager->persist($category);
            $manager->flush();
            $this->addReference("category$i", $category);
        }
    }

    public function loadCities(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $city = new City();
            $city->setName("City #$i");
            $manager->persist($city);
            $manager->flush();
            $this->addReference("city$i", $city);
        }
    }

    public function loadUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setFullName("User #$i");
            $user->setEmail("user$i@gmail.com");
            $user->setDeliveryAddress(rand(0, 1) == 1 ? "user address" : null);
            $user->setPhoneNumber("0" . strval(rand(600000000, 799999999)));
            $user->setPlainPassword('password');
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
            $user->setCity($this->getReference("city" . rand(0, 29)));
            $manager->persist($user);
            $manager->flush();
            $this->addReference("user$i", $user);
        }
    }

    public function loadProducts(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $product = new Product();
            $product->setName("Product #$i");
            $product->setDescription("Description for Product #$i");
            $product->setAvailability(true);
            $product->setPrice(rand(5, 35));
            $product->setStockQuantity(rand(1, 40));
            $product->addCategory($this->getReference("category" . rand(0, 29)));
            $manager->persist($product);
            $manager->flush();
            $this->addReference("product$i", $product);
        }
    }

    public function contactRequests(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $contactRequest = new ContactRequest();
            $contactRequest->setName("Contact request #$i");
            $contactRequest->setClient($this->getReference("user$i"));
            $contactRequest->setEmail("usercontact$i@gmail.com");
            $contactRequest->setMessage('Random message');
            $contactRequest->setPhoneNumber("0" . strval(rand(600000000, 799999999)));
            $manager->persist($contactRequest);
            $manager->flush();
        }
    }

    public function loadDeliveries(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $delivery = new Delivery();
            $delivery->setCity($this->getReference("city" . rand(0, 29)));
            $delivery->setDeliveryDate((new \DateTime())->modify("+" . rand(1, 30) . " days"));
            $manager->persist($delivery);
            $manager->flush();
        }
    }

    public function loadOrders(ObjectManager $manager): void
    {
        $statuses = ['pending', 'confirmed', 'refused', 'in preparation', 'delivered', 'canceled'];
        $paymentMethods = ['cash', 'card', 'check'];

        for ($i = 0; $i < 30; $i++) {
            $order = new Order();
            $order->setCustomer($this->getReference("user" . rand(0, 29)));

            // Set a random status string from the predefined list
            $order->setStatus($statuses[array_rand($statuses)]);

            // Set a random payment method string from the predefined list
            $order->setPaymentMethod($paymentMethods[array_rand($paymentMethods)]);

            $order->setTotalAmount((string) rand(50, 300));
            $order->setPaid((bool) rand(0, 1));

            $manager->persist($order);
            $manager->flush();

            $this->addReference("order$i", $order);

            // Create associated order items
            $this->loadOrderItems($manager, $order);
        }
    }

    public function loadOrderItems(ObjectManager $manager, Order $order): void
    {
        for ($i = 0; $i < rand(1, 5); $i++) {
            $orderItem = new OrderItems();
            $orderItem->setRelatedOrder($order);
            $orderItem->setProduct($this->getReference("product" . rand(0, 29)));
            $orderItem->setQuantity(rand(1, 10));

            $manager->persist($orderItem);
            $manager->flush();
        }
    }
}