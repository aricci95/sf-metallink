<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\LinkRepository;
use App\Repository\UserRepository;
use App\Service\IndicatorService;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Psr\Log\LoggerInterface;

class LinkService
{
    private $linkRepository;

    private $tokenStorage;

    private $links;

    private $cache;

    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LinkRepository $linkRepository,
        UserRepository $userRepository,
        LoggerInterface $logger
    ) {
        $this->cache          = new FilesystemCache('metallink', 3600);
        $this->tokenStorage   = $tokenStorage->getToken();
        $this->linkRepository = $linkRepository;
        $this->userRepository = $userRepository;
         $this->logger        = $logger;
    }

    public function getAll()
    {
        $currentUser = $this->tokenStorage->getUser();

        if (!($currentUser instanceof User)) {
            return [];
        }

        if (is_null($this->links)) {
            $this->load($currentUser);
        }

        return $this->links;
    }

    public function load(User $user)
    {
        if ($this->links) {
            return true;
        }

        $this->links = [];

        $cacheKey = LinkRepository::CACHE_KEY . $user->getId();

        if ($this->cache->has($cacheKey)) {
            $this->logger->debug('Get Links from cache for user ' . $user->getId());

            $this->links = unserialize($this->cache->get($cacheKey));
        } else {
            $this->logger->info('No cached indicators for user ' . $user->getId());

            foreach ($this->linkRepository->getLinks($user) as $link) {
                $this->links[$link->getLinkedUser($user)->getId()] = [
                    'id'        => $link->getId(),
                    'user_id'   => $link->getUser()->getId(),
                    'target_id' => $link->getTarget()->getId(),
                    'status'    => $link->getStatus(),
                ];
            }

            $this->cache->set($cacheKey, serialize($this->links));
        }
        
        return true;
    }

    public function flush(Link $link)
    {
        $this->logger->info('Flushing cached links for users ' . $link->getUser()->getId() . ' and ' . $link->getTarget()->getId());

        // Links
        $this->cache->delete(LinkRepository::CACHE_KEY . $link->getUser()->getId());
        $this->cache->delete(LinkRepository::CACHE_KEY . $link->getTarget()->getId());

        $this->logger->info('Flushing cached indicators for users ' . $link->getUser()->getId() . ' and ' . $link->getTarget()->getId());

        // Indicators
        $this->cache->delete(IndicatorService::CACHE_KEY . $link->getUser()->getId());
        $this->cache->delete(IndicatorService::CACHE_KEY . $link->getTarget()->getId());
    }

    public function isAccepted(User $user)
    {
        return $this->get($user)->isAccepted();
    }

    public function isBlacklisted(User $user)
    {
        return $this->get($user)->isBlacklisted();
    }

    public function isPending(User $user)
    {
        return $this->get($user)->isPending();
    }

    public function get(User $linkedUser)
    {
        $currentUser = $this->tokenStorage->getUser();

        if (!($currentUser instanceof User)) {
            return (new Link())->setTarget($linkedUser);
        }

        if (is_null($this->links)) {
            $this->load($currentUser);
        }

        if (!empty($this->links[$linkedUser->getId()])) {
            $storedLink = $this->links[$linkedUser->getId()];

            $user   = $this->userRepository->getReference($storedLink['user_id']);
            $target = $this->userRepository->getReference($storedLink['target_id']);

            $link = new Link();
            
            return $link
                ->setId($storedLink['id'])
                ->setUser($user)
                ->setTarget($target)
                ->setStatus($storedLink['status']);
        }

        $link = new Link();

        return $link
            ->setUser($currentUser)
            ->setTarget($linkedUser);
    }

    public function getBlacklist()
    {
        $blacklist = [];
        foreach ($this->getAll() as $link) {
            if ($link['status'] == Link::STATUS_BLACKLISTED) {
                $blacklist[] = $link['target_id'];
            }
        }

        return $blacklist;
    }
}
