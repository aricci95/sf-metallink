<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChatRepository extends ServiceEntityRepository implements SearchRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function hasNewChats(User $user): array
    {
        $results = $this->createQueryBuilder('chat')
            ->select('IDENTITY(chat.user) as user_id', 'IDENTITY(chat.target) as target_id')
            ->where('( chat.target = :user )')
            ->andWhere('chat.status = :status_new')
            ->setParameters([
                'user'       => $user,
                'status_new' => Chat::STATUS_NEW,
            ])
            ->getQuery()
            ->getScalarResult();

        $ids = [];
        foreach ($results as $result) {
            $ids[$result['user_id']]   = (int) $result['user_id'];
            $ids[$result['target_id']] = (int) $result['target_id'];
        }

        unset($ids[$user->getId()]);

        return array_values($ids);
    }

    public function getUsersChats(User $user, User $target): array
    {
        return $this->createQueryBuilder('chat')
            ->where('( chat.user = :user AND chat.target = :target OR chat.user = :target AND chat.target = :user )')
            ->andWhere('chat.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'target'         => $target,
                'status_deleted' => Chat::STATUS_DELETED,
            ])
            ->orderBy('chat.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getPreviousChats(User $user, User $target, Chat $lastChat = null): array
    {
        $qb = $this->createQueryBuilder('chat')
            ->where('( chat.user = :user AND chat.target = :target OR chat.user = :target AND chat.target = :user )')
            ->andWhere('chat.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'target'         => $target,
                'status_deleted' => Chat::STATUS_DELETED,
            ])
            ->orderBy('chat.id', 'DESC')
            ->setMaxResults(10);

        if ($lastChat) {
            $qb
                ->andWhere('chat.id > :last_chat_id')
                ->setParameter('last_chat_id', $lastChat->getId());
        }

        return array_reverse(
            $qb
                ->getQuery()
                ->getResult()
        );
    }

    public function countByUser(User $user, $status = null)
    {
        $qb = $this->createQueryBuilder('chat')
            ->select('COUNT(1)')
            ->where('chat.user = :target')
            ->setParameter('target', $user);

        if ($status) {
            $qb
                ->andWhere('chat.status = :status')
                ->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search(User $user, array $params = [], array $blacklist = [], $page = 1, $pageSize = 50)
    {
        $chats = $this->createQueryBuilder('chat')
            ->where('chat.user = :user OR chat.target = :user')
            ->andWhere('chat.status != :status_deleted')
            ->setParameters([
                'user'           => $user,
                'status_deleted' => Chat::STATUS_DELETED,
            ])
            ->orderBy('chat.createdAt', 'DESC')
            ->groupBy('chat.user, chat.target')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($chats as $chat) {
            if ($chat->getUser() != $user) {
                $results[$chat->getUser()->getId()] = $chat;
            } else {
                $results[$chat->getTarget()->getId()] = $chat;
            }
        }

        return $results;
    }

    public function searchCount(User $user, array $params = [], array $blacklist = [])
    {
        $qb = $this->createQueryBuilder('chat');

        if ($blacklist) {
            $qb
                ->andWhere('IDENTITY(chat.user) NOT IN ( :blacklist )')
                ->setParameter('blacklist', $blacklist);
        }

        return (int) $qb
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
