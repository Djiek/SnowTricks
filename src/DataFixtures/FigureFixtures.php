<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\TricksGroup;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Entity\Images;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 3; $i++) {
            $tricksGroup = new TricksGroup();
            if ($i === 1) {
                $tricksGroup->setName("Débutant");
            } elseif ($i === 2) {
                $tricksGroup->setName("Intermediaire");
            } elseif ($i === 3) {
                $tricksGroup->setName("Avancé");
            }
            $manager->persist($tricksGroup);
        }

        for ($k = 1; $k <= mt_rand(2, 4); $k++) {
            $user = new User();
            $user->setLogin("user n°$k")
                ->setPassword("1234")
                // ->setImages($image)
                ->setMail("user$k@gmail.com");
            $manager->persist($user);
        }
        for ($j = 1; $j <= mt_rand(4, 6); $j++) {
            $figure = new Figure();
            $figure->setName("figure n°$j")
                ->setDescription("description n°$j")
                ->setUser($user)
                ->setCreatedAt(new \DateTime())
                ->setTricksGroup($tricksGroup);
            $manager->persist($figure);
        }

        for ($m = 1; $m <= mt_rand(3, 7); $m++) {
            $image = new Images();
            $image->setFigure($figure)
                ->setLink("https://www.activeoutdoorpursuits.com/wp-content/uploads/2019/01/Active-Outdoor-Pursuits-Snowboarding-AOP-website-banner.jpg");
            $manager->persist($image);
        }

        for ($l = 1; $l <= mt_rand(3, 7); $l++) {
            $video = new Videos();
            $video->setFigure($figure)
                ->setLink("https://www.youtube.com/watch?v=8AWdZKMTG3U");
            $manager->persist($video);
        }

        for ($n = 1; $n <= mt_rand(3, 7); $n++) {
            $comment = new Comment();
            $comment->setContent("commentaire n°$n")
                ->setUser($user)
                ->setFigure($figure)
                ->setCreatedAt(new \DateTime());
            $manager->persist($comment);
        }
        $manager->flush();
    }
}
