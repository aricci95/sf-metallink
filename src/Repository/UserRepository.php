<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getReference($id)
    {
        return $this->getEntityManager()->getReference(User::class, $id);
    }

    public function search(array $params = [], array $blacklist = [])
    {
        $qb = $this->createQueryBuilder('user');

        if ($blacklist) {
            $qb
                ->andWhere('user.id NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }
        
        foreach ($params as $key => $value) {
            $qb
                ->andWhere('user.' . $key . ' = :' . $key)
                ->setParameter(':' . $key, $value);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
