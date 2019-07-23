<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    public const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello World'
        ], [
            'id' => 2,
            'slug' => 'another-post',
            'title' => 'Another Post'
        ], [
            'id' => 3,
            'slug' => 'last-example',
            'title' => 'Last Example'
        ],
    ];

    /**
     * @Route("/", name="blog_list")
     */
    public function list()
    {
        return $this->json(self::POSTS);
    }

    /**
     * @Route("/{id}", name="blog_by_id", requirements={"id"="\d+"})
     */
    public function post($id)
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/{slug}", name="blog_by_slug")
     */
    public function postBySlug($slug)
    {
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }
}
