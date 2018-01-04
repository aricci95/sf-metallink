<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\LinkRepository;
use Symfony\Component\Cache\Simple\FilesystemCache;

class LinkService
{
    private $linkRepository;

    private $tokenStorage;

    private $storedLinks = null;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LinkRepository $linkRepository
    ) {
        $this->cache          = new FilesystemCache();
        $this->tokenStorage   = $tokenStorage->getToken();
        $this->linkRepository = $linkRepository;
    }

    public function getAll()
    {
        return $this->storedLinks;
    }

    public function load(User $user)
    {
        if ($this->storedLinks) {
            return true;
        }

        $this->storedLinks = [];

        $cacheKey = 'links_' . $user->getId();

        if ($this->cache->has($cacheKey)) {
            $this->storedLinks = unserialize($this->cache->get($cacheKey));
        } else {
            foreach ($this->linkRepository->getLinks($user) as $link) {
                $this->storedLinks[$link->getUser()->getId()] = [
                    'user_id'   => $link->getUser(),
                    'target_id' => $link->getTarget(),
                    'status'    => $link->getStatus(),
                ];
            }

            $this->cache->set($cacheKey, serialize($this->storedLinks));
        }
        
        return true;
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

        if (is_null($this->storedLinks)) {
            $this->load($currentUser);
        }

        if (!empty($this->storedLinks[$linkedUser->getId()])) {
            $storedLink = $this->storedLinks[$linkedUser->getId()];

            $user   = $this->userRepository->getReference($storedLink['user_id']);
            $target = $this->userRepository->getReference($storedLink['target_id']);

            $link = new Link();
            
            return $link
                ->setUser($user)
                ->setTarget($target)
                ->setStatus($storedLink['status']);
            // $this->storedLinks[$linkedUser->getId()] = $this->tokenStorage->getUser()->getLink($linkedUser);
        }

        return $this->storedLinks[$linkedUser->getId()];
    }
}
