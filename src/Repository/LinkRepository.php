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

            case Link::STATUS_BLACKLISTED:
                $qb->andWhere('link.user = :user');
                break;

            case Link::STATUS_ACCEPTED:
                $qb->andWhere('link.user = :user OR link.target = :user');
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

            case Link::STATUS_BLACKLISTED:
                $qb->andWhere('link.user = :user');
                break;

            case Link::STATUS_ACCEPTED:
                $qb->andWhere('link.user = :user OR link.target = :user');
                break;

            default:
                $qb->andWhere('link.target = :user');
                break;
        }
        
        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}
