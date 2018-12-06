<?php

namespace App\DataFixtures;
use App\Entity\MicroPost;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadMicroPosts($manager);
        $this->loadUsers($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i < 10; $i++) { 
        	$microPost = new MicroPost();
        	$microPost->setText(' Some random text ' . rand(1, 100));
        	$microPost->setTime(new \DateTime('2018-12-03'));
        	$manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('saswatab');
        $user->setFullname('Saswata Banerjee');
        $user->setEmail('saswata@moviloglobal.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'saswata123'));

        $manager->persist($user);
        $manager->flush();
    }
}
