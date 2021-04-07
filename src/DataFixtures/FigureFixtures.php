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
    const TAB_IMAGE = [
        "9685ab8dbf9e19694fa35227d4064d21.jpeg",
        "96849537b73d44b50f8dccfed4936004.jpeg",
        "a3677699231c2f88ba5ed6fb0b2fdd72.jpeg",
        "b25cddf8eb0e440bae1f0d9a553b7fd3.jpeg",
        "bd134784dca3b440ec73e07dac65c0f5.jpeg",
        "e1cf7c43431c0291c305205be755a48a.jpeg"
    ];

    const TAB_VIDEO = [
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
        for ($m = 1; $m <= 30; $m++) {
            $image = new Images();
            $image->setFigure($figures[array_rand($figures)])
                ->setLink(self::TAB_IMAGE[array_rand(self::TAB_IMAGE)]);
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
