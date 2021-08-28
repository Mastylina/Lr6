<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DatabaseUsersFixtures extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $user_admin= new User();
        $user=new User();

        $user_admin->setEmail('anna@admin.com');
        $user_admin->setPassword($this->passwordEncoder->encodePassword($user_admin, 'qwe123'));
        $user_admin->setRoles(['ROLE_ADMIN']);

        $user->setEmail('artem@user.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'asd123'));

        $manager->persist($user_admin);
        $manager->persist($user);

        $manager->flush();
    }
}
