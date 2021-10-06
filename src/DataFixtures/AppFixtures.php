<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Faker\Factory;
use App\Entity\Trick;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	protected $slugger;

	public function __construct(SluggerInterface $slugger)
	{
		$this->slugger = $slugger;
	}
	public function load(ObjectManager $manager)
	{
		$faker = Factory::create('fr_FR');

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
