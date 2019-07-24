<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A first post!')
            ->setPublished(new \DateTime('2018-07-25 12:00:00'))
            ->setContent('Post content')
            ->setAuthor('Piotr Jura')
            ->setSlug('a-first-post');
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A second post!')
            ->setPublished(new \DateTime('2018-07-26 14:00:00'))
            ->setContent('Post content second')
            ->setAuthor('Jura Piotr')
            ->setSlug('a-second-post');
        $manager->persist($blogPost);


        $manager->flush();
    }
}
