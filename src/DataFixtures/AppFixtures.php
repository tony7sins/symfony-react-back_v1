<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A first post!')
            ->setPublished(new \DateTime('2018-07-25 12:00:00'))
            ->setContent('Post content')
            ->setAuthor($user)
            ->setSlug('a-first-post');
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost
            ->setTitle('A second post!')
            ->setPublished(new \DateTime('2018-07-26 14:00:00'))
            ->setContent('Post content second')
            ->setAuthor($user)
            ->setSlug('a-second-post');
        $manager->persist($blogPost);


        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    { }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();

        $user
            ->setUsername('admin')
            ->setName('Tony 7')
            ->setEmail('admin@admin.ru')

            ->setPassword('secret123#');

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
