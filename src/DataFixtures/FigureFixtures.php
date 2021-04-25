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
        "65b307746e0e0cfca9fc96bd6ae078cb.jpeg",
        "329b178893797487b201a082d8bdfc3f.jpeg",
        "739760d16869cd4bc81bda19012c85b1.jpeg",
        "a26e6d97941d17820eb217a1ec538728.jpeg",
        "a742b55bd980155cb3bfc5c291bf3650.jpeg",
        "b07919297168e2f76c9d5faae78d38b4.jpeg",
        "badd0e545d5251f856e32737c3aa49e1.jpeg",
        "d2a266dd2920758f2801ab78c7c6c72d.jpeg",
        'd93eb93b488ac1303a2d3135a781a21d.jpeg',
        '379fc4e0dcee03f3b2db6c3fba9b50e4.jpeg',
        'b7f66ee6b69474cbb39a34f33ba81b1e.jpeg',
        'e492e04b4c0dc0bf7f7b3236847d4664.jpeg'
    ];

    public const TAB_VIDEO = [
        "https://www.youtube.com/embed/5mtNg2mf-Hg",
        "https://www.youtube.com/embed/51sQRIK-TEI",
        "https://www.youtube.com/embed/iKkhKekZNQ8",
        "https://www.youtube.com/embed/_Qq-YoXwNQY"
    ];

    public const TAB_AVATAR = [
        '7be9ec98e24718e193edf3fe6b00ae47.jpeg',
        '52c899fce5d77e2296bc0d9ef3b56c58.jpeg',
        'cec2c6534486e016a3f460a278989771.jpeg',
        'f90b4966fa6ee07de27143714018def5.jpeg',
        '0ead2d839189b690d7e64883d02795fb.jpeg'
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
        for ($k = 0; $k < 5; $k++) {
            $user = new User();
            $user->setLogin($faker->firstName())
                ->setPassword($faker->password())
                ->setImage(self::TAB_AVATAR[$k])
                ->setMail($faker->email());
            $users[] = $user;
            $manager->persist($user);
        }

        $figures = [];
        for ($j = 1; $j <= 10; $j++) {
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
        for ($m = 0; $m < count(self::TAB_IMAGE); $m++) {
            $image = new Images();
            $image->setFigure($figures[array_rand($figures)])
                ->setLink(self::TAB_IMAGE[$m]);
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
