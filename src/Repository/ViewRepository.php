<?php

namespace App\Repository;

use App\Entity\View;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, View::class);
    }

    public function countByUser(User $user)
    {
        return $this->createQueryBuilder('view')
            ->select('COUNT(1)')
            ->where('view.target = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
