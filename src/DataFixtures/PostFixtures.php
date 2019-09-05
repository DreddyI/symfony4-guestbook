<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    /**
     * @var int
     */
    private const POST_COUNT = 30;

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::POST_COUNT; $i++) {
            $product = new Post("Post" . $i,'Anon');
            $manager->persist($product);
        }

        $manager->flush();
    }
}
