<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"})
     */
    public function list($page = 1, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        $limit = $request->get('limit', 10);

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'date' => array_map(
                    function (BlogPost $item) {
                        return $this->generateUrl(
                            "blog_by_id",
                            [
                                'slug' => $item->getSlug()
                            ]
                        );
                    },
                    $items
                )
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     */
    // @ParamConverter("post", class="App:BlogPost")
    public function post(BlogPost $post)
    {
        // Same as find($id)
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     */
    // @ParamConverter("post", class="App:BlogPost", options={"mapping" : {"slug" : "slug"}})
    public function postBySlug(BlogPost $post)
    {
        // Same as findOneBy(['slug' => $slug])
        return $this->json($post);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer
    ) {

        // /** @var Serializer $serializer */
        // $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        // $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }
}
