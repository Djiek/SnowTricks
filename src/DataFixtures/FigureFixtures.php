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
    public const TAB_IMAGE = [
        "1c629fa5d8d01aebb5b1ee0360963dd3.jpeg",
        "9a0ad48f1e4f57bacc63a5300dd6ab3a.jpeg",
        "9f7eafd6dd49e152d7d7b20b70e374a8.jpeg",
        "022cd46840078c17f44fea2edd4f8e90.jpeg",
        "055c18b23a6eae186de865be7b8d0ca3.jpeg",
        "65b307746e0e0cfca9fc96bd6ae078cb.jpeg",
        "098fc3f03afab0ea0bf45e746d429a86.jpeg",
        "112e30833a768cf3bb0a35dc870d8200.jpeg",
        "329b178893797487b201a082d8bdfc3f.jpeg",
        "689ab87c5ad91c5d120cd6209a3f556e.jpeg",
        "86382b8345d0d35919b967e830aa5dcd.jpeg",
        "739760d16869cd4bc81bda19012c85b1.jpeg",
        "a26e6d97941d17820eb217a1ec538728.jpeg",
        "a742b55bd980155cb3bfc5c291bf3650.jpeg",
        "b3a70140caaca5aba40f0f23ab6f0da6.jpeg",
        "b178603d777a1a5f7183f2f83fe8d228.jpeg",
        "b07919297168e2f76c9d5faae78d38b4.jpeg",
        "badd0e545d5251f856e32737c3aa49e1.jpeg",
        "d2a266dd2920758f2801ab78c7c6c72d.jpeg"
    ];

    public const TAB_VIDEO = [
        "https://www.youtube.com/embed/5mtNg2mf-Hg",
        "https://www.youtube.com/embed/51sQRIK-TEI",
        "https://www.youtube.com/embed/iKkhKekZNQ8",
        "https://www.youtube.com/embed/_Qq-YoXwNQY"
    ];


    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $tricksGroups = [];
        for ($i = 1; $i <= 3; $i++) {
            $tricksGroup = new TricksGroup();
            if ($i === 1) {
                $tricksGroup->setName("Débutant");
            } elseif ($i === 2) {
                $tricksGroup->setName("Intermediaire");
            } elseif ($i === 3) {
                $tricksGroup->setName("Avancé");
            }
            $tricksGroups[] = $tricksGroup;
            $manager->persist($tricksGroup);
        }

        $users = [];
        for ($k = 1; $k <= 5; $k++) {
            $user = new User();
            $user->setLogin($faker->firstName())
                ->setPassword($faker->password())
                ->setImage(self::TAB_IMAGE[array_rand(self::TAB_IMAGE, 1)])
                ->setMail($faker->email());
            $users[] = $user;
            $manager->persist($user);
        }

        $figures = [];
        for ($j = 1; $j <= 11; $j++) {
            $figure = new Figure();
            $figure->setName($faker->word())
                ->setSlug($figure->getName())
                ->setDescription($faker->sentence())
                ->setUser($users[array_rand($users)])
                ->setCreatedAt($faker->dateTimeBetween('-1 months'))
                ->setTricksGroup($tricksGroups[array_rand($tricksGroups)]);
            $figures[] = $figure;
            $manager->persist($figure);
        }

        $this->image($figures, $manager);

        $this->video($figures, $manager);

        $this->comment($figure, $faker, $manager, $figures, $users);

        $manager->flush();
    }

    public function image($figures, $manager)
    {
        for ($m = 1; $m <= 19; $m++) {
            $image = new Images();
            $image->setFigure($figures[array_rand($figures)])
                ->setLink(self::TAB_IMAGE[array_rand(self::TAB_IMAGE, 1)]);
            $manager->persist($image);
        }
    }

    public function video($figures, $manager)
    {
        for ($l = 1; $l <= 30; $l++) {
            $video = new Videos();
            $video->setFigure($figures[array_rand($figures)])
                ->setLink(self::TAB_VIDEO[array_rand(self::TAB_VIDEO)]);
            $manager->persist($video);
        }
    }

    public function comment($figure, $faker, $manager, $figures, $users)
    {
        for ($n = 1; $n <= 100; $n++) {
            $comment = new Comment();

            $interval = (new \DateTime())->diff($figure->getCreatedAt());
            $days = $interval->days;

            $comment->setContent($faker->sentence())
                ->setUser($users[array_rand($users)])
                ->setCreatedAt($faker->dateTimeBetween("-" . $days . 'days'))
                ->setFigure($figures[array_rand($figures)]);
            $manager->persist($comment);
        }
    }
}
