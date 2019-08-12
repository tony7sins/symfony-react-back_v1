<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserAdminController extends BaseAdminController
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /** @param User $entity */
    protected function persistEntity($entity)
    {

        $this->encodeUserPassword($entity);
        parent::persistEntity($entity);
    }

    /** @param User $entity */
    protected function updateEntity($entity)
    {

        $this->encodeUserPassword($entity);
        parent::updateEntity($entity);
    }

    /** @param User $entity */
    private function encodeUserPassword($entity)
    {
        $entity->setPassword(
            $this->passwordEncoder->encodePassword($entity, $entity->getPassword())
        );
    }
}
