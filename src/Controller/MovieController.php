<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Omdb\OmdbClient;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(methods="GET")
 */
class MovieController extends AbstractController
{
    /**
     * @var OmdbClient
     */
    private $api;

    public function __construct(OmdbClient $omdbClient)
    {
        $this->api = $omdbClient;
    }

    /**
     * @Route("/movie/latest", name="movie_latest")
     */
    public function latest(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('movie/latest.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/movie/search", name="movie_search")
     */
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword', 'Sky');
        $search = $this->api->requestBySearch($keyword);

        dump($this->api, $search);

        return $this->render('movie/search.html.twig', [
            'movies' => $search['Search'],
            'keyword' => $keyword,
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"}, methods={"POST"})
     */
    public function show(Request $request, $id,
                         MovieRepository $movieRepository,
                         UserRepository $userRepository,
                        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewType::class);
        $movie = $movieRepository->findOneBy(['id' => $id]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $review = $form->getData();
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
            $review
                ->setUser($user)
                ->setMovie($movie)
            ;

            $entityManager->persist($review);
            $entityManager->flush();

            dump($user, $movie);
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'review_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/movie/{imdbId}/import", name="movie_import", requirements={"imdbId": "tt\d+"})
     */
    public function import($imdbId, EntityManagerInterface $entityManager): Response
    {
        $movieFromApi = $this->api->requestById($imdbId);
        $movie = Movie::fromApi($movieFromApi);

        $entityManager->persist($movie);
        $entityManager->flush();

        return $this->redirectToRoute('movie_show', ['id' => $movie->getId()]);
    }
}
