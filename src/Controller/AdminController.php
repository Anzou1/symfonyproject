<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleType;
use App\DataFixtures\ArticlesFixtures;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
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
    public function adminFormArticle(Request $request, EntityManagerInterface $manager, Articles $article = null): Response
    {
        if(!$article)
        
        {
            $article = new Articles;
        }

        $form = $this->createForm(ArticleType::class, $article);
        

        $form->handleRequest($request);
        
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
            'editMode' => $article->getId()
        ]);
    }

    /**
     * @Route("/admin/{id}/delete-article", name="admin_delete_article")
     */
    public function deleteArticle(Articles $article, EntityManagerInterface $manager)
        {
            $manager->remove($article);
            $manager->flush();

            $this->addflash('success', "L'article a bien  été supprimé");

            return $this->redirectToRoute('admin_articles');
        }


    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function adminCategory(EntityManagerInterface $manager, CategoryRepository $repo, Request $request): Response
        {
            $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();

            $categories = $repo->findAll();
            dump($categories);

            

            return $this->render('admin/admin_category.html.twig', [
                'colonnes' => $colonnes,
                'categories' => $categories
            ]);
        }
    
        
    /**
     * @Route("/admin/category/new", name="admin_new_category")
     * @Route("/admin/{id}/edit-category", name="admin_edit_category") 
     */    
    public function adminFromCategory(Request $request, EntityManagerInterface $manager, Category $category = null): Response
    {
        if (!$category)
        {
        $category = new Category;
        }

        $formC = $this->createForm(CategoryType::class, $category);

        $formC->handleRequest($request);

        if($formC->isSubmitted() && $formC->isValid())
        {
            if (!$category->getId())
            {
                $message = 'La catégorie a bien été enregistrée';
            }
            else
            {
                $message = 'La catégorie a bien été modifiée';
            }
            $manager->persist($category);
            $manager->flush();

            $this->addflash('success', $message);

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/admin_create_category.html.twig', [
            'formCategory' => $formC->createView(),
            'editMode' => $category->getId()
        ]);
    }  
    
    
    /**
     * @Route("admin/{id}/delete-category", name="admin_delete_category")
     */
    public function adminDeleteCatégory(Category $category, EntityManagerInterface $manager)
    {
        if($category->getArticle()->isEmpty())
        {
            $manager->remove($category);
            $manager->flush();

            $this->addflash('success', "La catégorie a bien  été supprimée");
        }
        else
        {
            $this->addflash('danger', "Il n'est pas possible de supprimer cette catégorie car des articles y sont toujours associés");
        }


        return $this->redirectToRoute('admin_category');
    }
}
