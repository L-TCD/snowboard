<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	protected $slugger;
	protected $encoder;


	public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
	{
		$this->slugger = $slugger;
		$this->encoder = $encoder;
	}
	public function load(ObjectManager $manager)
	{
		$faker = Factory::create('fr_FR');

		$admin = new User();

		$hash = $this->encoder->hashPassword($admin, "password");

		$admin
			->setEmail("admin@gmail.com")
			->setPassword($hash)
			->setRoles(['ROLE_ADMIN']);

		$manager->persist($admin);

		$users = [];

		for ($u = 0; $u < 5; $u++) {
			$user = new User();

			$hash = $this->encoder->hashPassword($user, "password");

			$user
				->setEmail("user$u@gmail.com")
				->setPassword($hash);

			$users[] = $user;

			$manager->persist($user);
		}

		for ($i = 0; $i < 20; $i++) {
			$trick = new Trick();
			$trick->setName($faker->sentence(mt_rand(1, 3)))
				->setSlug(strtolower($this->slugger->slug($trick->getName())))
				->setDescription($faker->paragraph());

			$manager->persist($trick);

			for ($j = 0; $j < mt_rand(1, 10); $j++) {
				$image = new Image();
				$image->setName('fig.jpg')
					->setTrick($trick);

				$manager->persist($image);
			}
		}

		$manager->flush();
	}
}
