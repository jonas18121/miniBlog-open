<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const DEFAULT_USER = ['email' => 'benopen@ben.open', 'password' => 'password'];
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $fake = Factory::create();

        $defaultUser = new User();
        $passHash = $this->encoder->encodePassword($defaultUser, self::DEFAULT_USER['password']);

        $defaultUser->setEmail(self::DEFAULT_USER['email'])
            ->setPassword($passHash);

        $manager->persist($defaultUser);

        for ($u = 0; $u < 10; ++$u) {
            $user = new User();

            $passHash = $this->encoder->encodePassword($user, 'password');

            $user->setEmail($fake->email)
                ->setPassword($passHash);

            if (0 === $u % 3) {
                $user->setStatus(false)
                    ->setAge(23);
            }

            $manager->persist($user);

            for ($a = 0; $a < random_int(5, 15); ++$a) {
                $article = (new Article())->setAuthor($user)
                    ->setContent($fake->text(300))
                    ->setName($fake->text(50));

                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
