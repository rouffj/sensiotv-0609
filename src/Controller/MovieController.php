<?php

namespace App\Controller;

use App\Omdb\OmdbClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route(methods="GET")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movie/latest", name="movie_latest")
     */
    public function latest(): Response
    {
        return $this->render('movie/latest.html.twig');
    }

    /**
     * @Route("/movie/search", name="movie_search")
     */
    public function search(HttpClientInterface $httpClient, Request $request): Response
    {
        $apiToken = '28c5b7b1';
        $omdHost = 'http://www.omdbapi.com/';
        $api = new OmdbClient($httpClient, $apiToken, $omdHost);
        $keyword = $request->query->get('keyword', 'Sky');
        $search = $api->requestBySearch($keyword);

        dump($api, $search);

        return $this->render('movie/search.html.twig', [
            'movies' => $search['Search'],
            'keyword' => $keyword,
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"})
     */
    public function show($id): Response
    {
        return $this->render('movie/show.html.twig');
    }
}
