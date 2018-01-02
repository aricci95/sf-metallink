<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Link::class);
    }

    public function getLinks(User $user): array
    {
         return $this->createQueryBuilder('link')
            ->where('link.user = :user OR link.target = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
