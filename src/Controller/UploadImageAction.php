<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;

class UploadImageAction
{
    /** @var FormFactoryInterface $formFactoryInterface */
    private $formFactoryInterface;

    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * @var ValidatorInterface $validatorInterface
     */
    private $validator;

    public function __construct(
        FormFactoryInterface $formFactoryInterface,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {

        $this->formFactoryInterface = $formFactoryInterface;
        $this->em                   = $em;
        $this->validator            = $validator;
    }

    public function __invoke(Request $request)
    {
        $image = new Image();

        $form = $this->formFactoryInterface->create(null, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($image);
            $this->em->flush();

            $image->setFile(null);
            return $image;
        }

        throw new ValidationException(
            $this->validator->validate($image)
        );
    }
}
