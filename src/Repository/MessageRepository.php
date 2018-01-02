<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getUsersMessages(User $user, User $target): array
    {
        return $this->createQueryBuilder('message')
            ->where('message.user = :user OR message.user = :target')
            ->andWhere('message.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'target'         => $target,
                'status_deleted' => Message::STATUS_DELETED,
            ])
            ->orderBy('message.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByUser(User $user, $status = null)
    {
        $qb = $this->createQueryBuilder('message')
            ->select('COUNT(1)')
            ->where('message.user = :target')
            ->setParameter('target', $user);

        if ($status) {
            $qb
                ->andWhere('message.status = :status')
                ->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }
}
