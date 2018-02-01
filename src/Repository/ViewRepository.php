<?php

namespace App\Repository;

use App\Entity\View;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewRepository extends ServiceEntityRepository implements SearchRepositoryInterface
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

    public function search(User $user, array $params = [], array $blacklist = [], $page = 1, $pageSize = 50)
    {
        $qb = $this->createQueryBuilder('view');

        $blacklist[] = $user;

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(view.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }

        return $qb
            ->setFirstResult($page * $pageSize - $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }

    public function searchCount(User $user, array $params = [], array $blacklist = [])
    {
        $qb = $this->createQueryBuilder('view');

        $blacklist[] = $user;

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(view.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }
        
        return (int) $qb
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
