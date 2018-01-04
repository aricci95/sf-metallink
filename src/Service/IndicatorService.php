<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\LinkRepository;
use App\Repository\MessageRepository;
use App\Repository\ViewRepository;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Psr\Log\LoggerInterface;

class IndicatorService
{
    const CACHE_KEY   = 'indicator_';

    const TYPE_LINK    = 'link';
    const TYPE_MESSAGE = 'message';
    const TYPE_VIEW    = 'view';

    private $linkRepository;

    private $messageRepository;

    private $viewRepository;

    private $tokenStorage;

    private $indicators;

    private $cache;

    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LinkRepository $linkRepository,
        MessageRepository $messageRepository,
        ViewRepository $viewRepository,
        LoggerInterface $logger
    ) {
        $this->cache             = new FilesystemCache('metallink', 3600);
        $this->tokenStorage      = $tokenStorage->getToken();
        $this->linkRepository    = $linkRepository;
        $this->messageRepository = $messageRepository;
        $this->viewRepository    = $viewRepository;
        $this->logger            = $logger;
    }

    public function getAll()
    {
        $user = $this->tokenStorage->getUser();

        if (!($user instanceof User)) {
            $this->indicators = [
                self::TYPE_LINK . '_sent'        => 0,
                self::TYPE_LINK . '_accepted'    => 0,
                self::TYPE_LINK . '_blacklisted' => 0,

                self::TYPE_MESSAGE               => 0,
                self::TYPE_VIEW                  => 0,
            ];
        }

        if (is_null($this->indicators)) {
            $this->count($user);
        }

        return $this->indicators;
    }

    public function count(User $user)
    {
        $cacheKey = self::CACHE_KEY . $user->getId();

        if ($this->cache->has($cacheKey)) {
            $this->logger->debug('Get indicators from cache for user ' . $user->getId());

            $this->indicators = unserialize($this->cache->get($cacheKey));
        } else {
            $this->logger->info('No cached indicators for user ' . $user->getId());

            $this->indicators = [
                // Link states
                self::TYPE_LINK . '_sent'        => $this->linkRepository->countByUser($user, Link::STATUS_PENDING),
                self::TYPE_LINK . '_accepted'    => $this->linkRepository->countByUser($user, Link::STATUS_ACCEPTED),
                self::TYPE_LINK . '_blacklisted' => $this->linkRepository->countByUser($user, Link::STATUS_BLACKLISTED),
                // Messages
                self::TYPE_MESSAGE               => $this->messageRepository->countByUser($user, Message::STATUS_NEW),
                // Views
                self::TYPE_VIEW                  => $this->viewRepository->countByUser($user),
            ];

            foreach ($this->indicators as $key => $value) {
                if (!$value) {
                    $this->indicators[$key] = 0;
                }
            }

            $this->cache->set($cacheKey, serialize($this->indicators));
        }

        return true;
    }

    public function flush(User $user)
    {
        $this->logger->info('Flushing cached indicators for user ' . $user->getId());

        $this->cache->delete(self::CACHE_KEY . $user->getId());
    }

    public function isAccepted(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_ACCEPTED;
    }

    public function isBlacklisted(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_BLACKLISTED;
    }

    public function isPending(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_PENDING;
    }
}
