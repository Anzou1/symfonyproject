<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager)

    /**
     * les fixtures prtmettent de creer des données fictives en bdd.
     */
    {
        for($i = 1; $i <= 10; $i++)
        {
            // pour chaque tour de boucle on crée un objet Article vide
            $article = new Articles;

            $article->setTitle("Titre de l'article n°$i")
                    ->setContent("<p>Contenu de l'article n°$i")
                    ->setImage("https://picsum.photos/200/300")
                    ->setCreatedAt(new \DateTime());

            //ObjectManager permet de manipuler les lignes ds la bdd (insert, update...)
            //persist() prepare les requete d'insertions
            $manager->persist($article);        
        }
        //flush() libere l'insertion en bdd
        $manager->flush();

    }
}
