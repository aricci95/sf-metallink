<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LinkService
{
    private $user;

    private $storedLinks = [];

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
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
            $this->storedLinks[$target->getId()] = $this->user->getLink($target);
        }

        return $this->storedLinks[$target->getId()];
    }
}
