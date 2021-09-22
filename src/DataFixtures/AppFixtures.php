<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
		// create 20 tricks! Bam!
		for ($i = 0; $i < 20; $i++) {
			$product = new Trick();
			$product->setName('Figure ' . $i);
			$product->setSlug($product->getName().'-'.$product->getId());
			$product->setDescription('Phrase de test '. $product->getId());
			$manager->persist($product);
		}

        $manager->flush();
    }
}
