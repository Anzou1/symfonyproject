<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleType;
use App\DataFixtures\ArticlesFixtures;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticlesRepository $repo): Response
    {
        $colonnes = $manager->getClassMetadata(Articles::class)->getFieldNames();
        dump($colonnes);

        $articles = $repo->findAll();
        
        
        return $this->render('admin/admin_articles.html.twig', [
            'admin_articles' => $articles,
            'colonnes' => $colonnes
        ]);
    }

    /**
     * @Route("/admin/article/new", name="admin_new_article")
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function adminForm(Request $request, EntityManagerInterface $manager, Articles $article = null): Response
    {
        if(!$article)
        
        {
            $article = new Articles;
        }

        $form = $this->createForm(ArticleType::class, $article);
        

        $form->handleRequest($request);
        dump($request);
        if($form->isSubmitted() && $form->isvalid()){

            if(!$article->getId()){

                $article->setCreatedAt(new \DateTime());
                $this->addflash('success', "L'article a bien  été enregistré");
            }

            $manager->persist($article);
            $manager->flush();
            

            $this->addflash('success', "L'article a bien  été modifié");

            return $this->redirectToRoute('admin_articles');
        }   


        return $this->render('admin/admin_create.html.twig', [
            'formArticle' => $form->createView(),
            'article' => $article
        ]);
    }
}
