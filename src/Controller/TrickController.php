<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trick')]
class TrickController extends AbstractController
{
	#[Route('/', name: 'trick_index', methods: ['GET'])]
	public function index(TrickRepository $trickRepository): Response
	{
		return $this->render('trick/index.html.twig', [
			'tricks' => $trickRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'trick_new', methods: ['GET', 'POST'])]
	public function new(Request $request): Response
	{
		$trick = new Trick();
		$form = $this->createForm(TrickType::class, $trick);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$images = $form->get('images')->getData();

			foreach ($images as $image) {
				$file = md5(uniqid()) . '.' . $image->guessExtension();
				$image->move(
					$this->getParameter('images_directory'),
					$file
				);

				$img = new Image();
				$img->setName($file);
				$trick->addImage($img);
			}

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($trick);
			$entityManager->flush();

			return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('trick/new.html.twig', [
			'trick' => $trick,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'trick_show', methods: ['GET'])]
	public function show(Trick $trick): Response
	{
		return $this->render('trick/show.html.twig', [
			'trick' => $trick,
		]);
	}

	#[Route('/{id}/edit', name: 'trick_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Trick $trick): Response
	{
		$form = $this->createForm(TrickType::class, $trick);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$images = $form->get('images')->getData();

			foreach ($images as $image) {
				$file = md5(uniqid()) . '.' . $image->guessExtension();
				$image->move(
					$this->getParameter('images_directory'),
					$file
				);

				$img = new Image();
				$img->setName($file);
				$trick->addImage($img);
			}

			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('trick/edit.html.twig', [
			'trick' => $trick,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'trick_delete', methods: ['POST'])]
	public function delete(Request $request, Trick $trick): Response
	{
		if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove($trick);
			$entityManager->flush();
		}

		return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @Route("/delete/image/{id}", name="trick_delete_image", methods={"DELETE"})
	 */
	public function deleteImage(Image $image, Request $request)
	{
		$data = json_decode($request->getContent(), true);

		// On vérifie si le token est valide
		if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
			// On récupère le nom de l'image
			$fileName = $image->getName();
			// On supprime le fichier
			unlink($this->getParameter('images_directory') . '/' . $fileName);

			// On supprime l'entrée de la base
			$em = $this->getDoctrine()->getManager();
			$em->remove($image);
			$em->flush();

			// On répond en json
			return new JsonResponse(['success' => 1]);
		} else {
			return new JsonResponse(['error' => 'Token Invalide'], 400);
		}
	}

	/**
	 * @Route("/fiche-detaillee/{slug}", name="slug_trick")
	 */
	public function slugTrickShow($slug, TrickRepository $trickRepository): Response
	{

		$trick = $trickRepository->findOneBy([
			'slug' => $slug,
		]);

		if (!$trick) {
			throw $this->createNotFoundException("La figure demandée n'existe pas.");
		}

		return $this->render('trick/show.html.twig', [
			'trick' => $trick,
		]);
	}
}
