<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Articles;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\DataFixtures\ArticlesFixtures;
use App\Entity\User;
use App\Form\AdminRegistrationType;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
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
    public function adminCategory(EntityManagerInterface $manager, CategoryRepository $repo): Response
        {
            $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();

            $categories = $repo->findAll();
            

            

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

    /**
     * @Route("/admin/admin-comments", name="admin_comments")
     */
    public function adminComments(EntityManagerInterface $manager, CommentRepository $repo): Response
    {
        $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();

        $cellule = $repo->findAll();
        

        return $this->render('admin/admin_comments.html.twig', [
            'NomColonne' => $colonnes,
            'cellules' => $cellule
        ]);
    }

    /**
     * @Route("/admin/{id}/edit-comments", name="admin_edit_comments")
     */  
    public function adminFormComment(Request $request, CommentRepository $repo, Comment $comment, EntityManagerInterface $manager): Response    
    {
        
        
        $formCom = $this->createForm(AdminCommentType::class,$comment);

        $formCom->handleRequest($request);

        if ($formCom->isSubmitted() && $formCom->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addflash('success', "Le commentaire a bien  été modifié");

            return $this->redirectToRoute('admin_comments');
        }

        return $this->render('admin/admin_edit_comments.html.twig', [
            'formCom' => $formCom->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}/delete-comment", name="admin_delete_comment")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addflash('success', "Le commentaire a bien  été supprimé");

        return $this->redirectToRoute('admin_comments');


    }


    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminUsers(EntityManagerInterface $manager, UserRepository $repo): Response

    {
        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();

        $users = $repo->findAll();

        return $this->render('admin/admin_users.html.twig', [
            'titre' => $colonnes,
            'users' => $users
            
        ]);
    }

    /**
     * @Route("/admin/{id}/edit-user", name="admin_edit_user")
     */
    public function editUser(User $user, Request $request,EntityManagerInterface $manager): Response
    {
       

        $formUser = $this->createForm(AdminRegistrationType::class, $user);
        dump($formUser);

        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addflash('success', "L'utlilisateur à été modifié");

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit-user.html.twig', [
            'formUser' => $formUser->createView()
        ]);
        
    }

     /**
     * @Route("/admin/{id}/delete-user", name="admin_delete_user")
     */
    public function deleteUser(User $users, EntityManagerInterface $manager): Response
    {
        $manager->remove($users);
        $manager->flush();

        $this->addflash('success', "L'utilisateur a bien  été supprimé");

        return $this->redirectToRoute('admin_users');


    }

    
}
