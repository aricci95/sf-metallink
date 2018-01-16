<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LinkRepository extends ServiceEntityRepository
{
    const CACHE_KEY = 'link_user_';

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

    public function getByUser(User $user, $status): array
    {
        $qb = $this
            ->createQueryBuilder('link')
            ->where('link.status = :status')
            ->setParameter('status', $status)
            ->setParameter('user', $user)
            ->orderBy('link.createdAt', 'DESC');
        
        switch ($status) {
            case Link::STATUS_PENDING:
                $qb->andWhere('link.target = :user');
                break;

            case Link::STATUS_ACCEPTED:
                $qb->andWhere('link.user = :user OR link.target = :user');
                break;
            
            case Link::STATUS_BLACKLISTED:
                $qb->andWhere('link.user = :user');
                break;
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function countByUser(User $user, $status)
    {
        $qb = $this->createQueryBuilder('link')
            ->select('COUNT(1)')
            ->where('link.status = :status')
            ->setParameter('status', $status)
            ->setParameter('user', $user);

        switch ($status) {
            case Link::STATUS_PENDING:
                $qb->andWhere('link.target = :user');
                break;

            case Link::STATUS_ACCEPTED:
                $qb->andWhere('link.user = :user OR link.target = :user');
                break;

            case Link::STATUS_BLACKLISTED:
                $qb->andWhere('link.user = :user');
                break;
        }
        
        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search(array $params = [], array $blacklist = [], $page = 1, $pageSize = 50)
    {
        $qb = $this->createQueryBuilder('link')
            ->where('link.status = :status')
            ->setParameter('status', $params['status']);

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(link.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }


        return $qb
            
            ->setFirstResult($page * $pageSize - $pageSize)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }

    public function searchCount(array $params = [], array $blacklist = [])
    {
        $qb = $this->createQueryBuilder('link')
            ->where('link.status = :status')
            ->setParameter('status', $params['status']);

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(link.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }

        return (int) $qb
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
