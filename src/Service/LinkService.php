<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\LinkRepository;

class LinkService
{
    private $linkRepository;

    private $tokenStorage;

    private $storedLinks = [];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LinkRepository $linkRepository
    ) {
        $this->tokenStorage   = $tokenStorage->getToken();
        $this->linkRepository = $linkRepository;
    }

    public function getLinks(User $user = null)
    {
        if (!$user) {
            $user = $this->tokenStorage->getUser();
        }

        if (empty($this->storedLinks)) {
            foreach ($this->linkRepository->getLinks($user) as $link) {
                $this->storedLinks[$link->getUser()->getId()] = $link;
            }
        }
        
        return $this->storedLinks;
    }

    public function isAccepted(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_VALIDATED;
    }

    public function isBlacklisted(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_BLACKLISTED;
    }

    public function isPending(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_PENDING;
    }

    public function getLink(User $target)
    {
        if (empty($this->storedLinks[$target->getId()])) {
            $this->storedLinks[$target->getId()] = $this->tokenStorage->getUser()->getLink($target);
        }

        return $this->storedLinks[$target->getId()];
    }
}
