<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    //chaque methode du controller est associé à une route bien spécifique. lorsque nous envoyons la route '/blog' dans l'url du navigateur, cela execute automatiquement dans le controlle la methode associé a celle ci.
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * @Route("/", name="home");
     */

    public function home() :Response {
        return $this->render('blog/home.html.twig', ['title' => 'Bienvenue', 
        'age' => 30]);
    }

    /**
     * @Route("/blog/12", name="blog_show")
     */
    public function show(): Response{
        return $this->render('blog/show.html.twig');
    }
}
