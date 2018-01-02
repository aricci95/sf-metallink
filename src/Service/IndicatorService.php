<?php

namespace App\Service;

use App\Entity\Link;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\LinkRepository;
use App\Repository\MessageRepository;
use App\Repository\ViewRepository;

class IndicatorService
{
    const TYPE_LINK    = 'link';
    const TYPE_MESSAGE = 'message';
    const TYPE_VIEW    = 'view';

    private $linkRepository;

    private $messageRepository;

    private $viewRepository;

    private $tokenStorage;

    private $storedLinks = [];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LinkRepository $linkRepository,
        MessageRepository $messageRepository,
        ViewRepository $viewRepository
    ) {
        $this->tokenStorage      = $tokenStorage->getToken();
        $this->linkRepository    = $linkRepository;
        $this->messageRepository = $messageRepository;
        $this->viewRepository    = $viewRepository;
    }

    public function count($type)
    {
        $user = $this->tokenStorage->getUser();

        switch ($type) {
            case self::TYPE_LINK . '_sent':
                return $this->linkRepository->countByUser($user, Link::STATUS_PENDING);
                break;

            case self::TYPE_LINK . '_accepted':
                return $this->linkRepository->countByUser($user, Link::STATUS_ACCEPTED);
                break;

            case self::TYPE_LINK . '_blacklisted':
                return $this->linkRepository->countByUser($user, Link::STATUS_BLACKLISTED);
                break;

            case self::TYPE_MESSAGE:
                return $this->messageRepository->countByUser($user, Message::STATUS_NEW);

            case self::TYPE_VIEW:
                return $this->viewRepository->countByUser($user);
                break;
        }
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
