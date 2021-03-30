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
        for ($k = 1; $k <= 3; $k++) {
            $user = new User();
            $user->setLogin($faker->firstName())
                ->setPassword($faker->password())
                ->setImage($faker->imageUrl())
                ->setMail($faker->email());
            $users[] = $user;
            $manager->persist($user);
        }

        $figures = [];
        for ($j = 1; $j <= 5; $j++) {
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

        $this->image($figures, $faker, $manager);

        $this->video($figures, $faker, $manager);

        $this->comment($figure, $faker, $manager, $figures, $users);

        $manager->flush();
    }

    public function image($figures, $faker, $manager)
    {
        for ($m = 1; $m <= mt_rand(3, 15); $m++) {
            $image = new Images();
            $image->setFigure($figures[array_rand($figures)])
                ->setLink($faker->imageUrl());
            $manager->persist($image);
        }
    }

    public function video($figures, $faker, $manager)
    {
        for ($l = 1; $l <= mt_rand(3, 10); $l++) {
            $video = new Videos();
            $video->setFigure($figures[array_rand($figures)])
                ->setLink($faker->imageUrl());
            $manager->persist($video);
        }
    }

    public function comment($figure, $faker, $manager, $figures, $users)
    {
        for ($n = 1; $n <= mt_rand(3, 7); $n++) {
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
