<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Articles;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    //chaque methode du controller est associé à une route bien spécifique. lorsque nous envoyons la route '/blog' dans l'url du navigateur, cela execute automatiquement dans le controlle la methode associé a celle ci.
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticlesRepository $repo): Response
    {
        //$repo = $this->getDoctrine()->getRepository(Articles::class); equivalent à public function index(ArticlesRepository $repo): {}
        
        // findAll() est une methode issue de la classs ArticleRepository et permet de selectionner l'ensemble d'une table sql (SELECT*FROM)
        $articles = $repo->findAll();
        //dump($articles);
        return $this->render('blog/index.html.twig', [
            'articles' => $articles// Nous envoyons sur le template les articles selectionnés en bdd
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
     * Nous utilisons le concepte de route paramétrées pour faire en sorte de récupérer le bon id du bon article. Nous avons définis le paramètre de type{id} directement dans la route.
     */

     /**
     * @Route("/blog/new/", name="blog_create")
     *  @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Articles $article = null, Request $request, EntityManagerInterface $manager)
    {
        /*
        
        if($request->request->count() > 0){
            $articles = new Articles;
            $articles->setTitle($request->request->get('title'))
                     ->setContent($request->request->get('content'))
                     ->setImage($request->request->get('image'))
                     ->setCreatedAt(new \DateTime());

            $manager->persist($articles);
            $manager->flush();   
            
            return $this->redirectToRoute('blog_show', ['id' => $articles->getid() ]);

        }*/
        if(!$article){
        $article = new Articles;
        }

        /* $form = $this->createFormBuilder($article)
                     ->add('title')

                     ->add('content'// TextType::class, [
                         //'attr' => [
                             //'placeholder' => "contenu de l'article"
                         //]]
                         )

                     ->add('image')

                     ->getForm();
        */
        $form = $this->createForm(ArticleType::class, $article);
       

        $form->handleRequest($request);
        dump($request);
        if($form->isSubmitted() && $form->isvalid()){

            if(!$article->getId()){

                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()
            ]);
        }            


        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null// si id article diff de null, alors 'editMode' renvoi true et que c'est une moddification.
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Articles $article, Request $request, EntityManagerInterface $manager): Response{

        // on appel le repository de la classe Article afin de selectionner dans la table Article.
        //$repo = $this->getDoctrine()->getRepository(Articles::class);

        //$article = $repo->find($id);
        $comment = new Comment;
        dump($request);
        $formComment = $this->createForm(CommentType::class, $comment);

        

        $formComment->handleRequest($request);
       

        if($formComment->isSubmitted() && $formComment->isvalid()){

            if(!$comment->getId()){

                $comment->setCreatedAt(new \DateTime());
                $comment->setArticle($article);
            }

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire a bien été posté");

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
           

            
        }      
        
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'formComment' => $formComment->createView()
            ]);


       
    
        

        return $this->render('blog/show.html.twig', [
            'formComment' => $formComment->createView(),
            'article' => $comment// Nous envoyons sur le template les articles selectionnés en bdd
        ]);
    }

    
}
