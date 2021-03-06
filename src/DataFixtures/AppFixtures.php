<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Security\TokenGenerator;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username'  => 'admin',
            'email'     => 'admin@blog.com',
            'name'      => 'Piotr Jura',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_SUPERADMIN],
            'enabled'   => true,
        ],
        [
            'username'  => 'john_doe',
            'email'     => 'john@blog.com',
            'name'      => 'John Doe',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_ADMIN],
            'enabled'   => true,
        ],
        [
            'username'  => 'rob_smith',
            'email'     => 'rob@blog.com',
            'name'      => 'Rob Smith',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_WRITER],
            'enabled'   => true,
        ],
        [
            'username'  => 'jenny_rowling',
            'email'     => 'jenny@blog.com',
            'name'      => 'Jenny Rowling',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_WRITER],
            'enabled'   => true,
        ],

        [
            'username'  => 'han_solo',
            'email'     => 'han_solo@blog.com',
            'name'      => 'Han Solo',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_EDITOR],
            'enabled'   => false,
        ],
        [
            'username'  => 'jedi_knight',
            'email'     => 'jedi_knight@blog.com',
            'name'      => 'J edi Knight',
            'password'  => 'secret123#',
            'roles'     => [User::ROLE_COMMENTATOR],
            'enabled'   => true,
        ]
    ];

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /** @var \Faker\Factory $faker */
    private $faker;

    /** @var TokenGenerator $tokenGenerator */
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator
    ) {

        $this->encoder          = $encoder;
        $this->faker            = \Faker\Factory::create();
        $this->tokenGenerator   = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {

        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {

        foreach (self::USERS as $userFixture) {

            $user = new User();
            $user
                ->setUsername($userFixture['username'])
                ->setName($userFixture['name'])
                ->setEmail($userFixture['email'])

                ->setPassword($this->encoder->encodePassword(
                    $user,
                    $userFixture['password']
                ))
                ->setRoles($userFixture['roles'])
                ->setEnabled($userFixture['enabled']);

            if (!$userFixture['enabled']) {

                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function loadBlogPosts(ObjectManager $manager)
    {

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost
                ->setTitle($this->faker->realText(30))
                ->setPublished($this->faker->dateTimeThisYear)
                ->setContent($this->faker->realText)
                ->setAuthor($this->getRandomUserReference($blogPost))
                ->setSlug($this->faker->slug);

            $this->setReference("blog_post_{$i}", $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {

        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment;
                $comment
                    ->setContent($this->faker->realText())
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setAuthor($this->getRandomUserReference($comment))
                    ->setBlogPost($this->getReference("blog_post_{$i}"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    private function getRandomUserReference($entity): User
    {

        $randomUser = self::USERS[rand(0, 5)];

        if ($entity instanceof BlogPost && !count(array_intersect(
            $randomUser['roles'],
            [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER]
        ))) {
            return $this->getRandomUserReference($entity);
        }

        if ($entity instanceof Comment && !count(array_intersect(
            $randomUser['roles'],
            [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER, User::ROLE_COMMENTATOR]
        ))) {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference("user_" . $randomUser['username']);
    }
}
