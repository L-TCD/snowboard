<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
	 * show homepage
     * @Route("/", name="home")
     */
    public function index(): Response
    {
		$arrayTest = ['a', 'b', 'c'];

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
			'letters' => $arrayTest,
        ]);
    }
}
