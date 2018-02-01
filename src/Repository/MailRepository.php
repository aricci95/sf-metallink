<?php

namespace App\Repository;

use App\Entity\Mail;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MailRepository extends ServiceEntityRepository implements SearchRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mail::class);
    }

    public function getUsersMails(User $user, User $target): array
    {
        return $this->createQueryBuilder('mail')
            ->where('( mail.user = :user AND mail.target = :target OR mail.user = :target AND mail.target = :user )')
            ->andWhere('mail.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'target'         => $target,
                'status_deleted' => Mail::STATUS_DELETED,
            ])
            ->orderBy('mail.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByUser(User $user, $status = null)
    {
        $qb = $this->createQueryBuilder('mail')
            ->select('COUNT(1)')
            ->where('mail.user = :target')
            ->setParameter('target', $user);

        if ($status) {
            $qb
                ->andWhere('mail.status = :status')
                ->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search(User $user, array $params = [], array $blacklist = [], $page = 1, $pageSize = 50)
    {
        $mails = $this->createQueryBuilder('mail')
            ->where('mail.user = :user OR mail.target = :user')
            ->andWhere('mail.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'status_deleted' => Mail::STATUS_DELETED,
            ])
            ->orderBy('mail.createdAt', 'DESC')
            ->groupBy('mail.user, mail.target')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($mails as $mail) {
            if ($mail->getUser() != $user) {
                $results[$mail->getUser()->getId()] = $mail;
            } else {
                $results[$mail->getTarget()->getId()] = $mail;
            }
        }

        return $results;
    }

    public function searchCount(User $user, array $params = [], array $blacklist = [])
    {
        $qb = $this->createQueryBuilder('mail');

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(mail.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }

        return (int) $qb
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
