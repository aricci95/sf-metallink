<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 * @ORM\Table(name="chat")
 */
class Chat extends Message
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="chatsSent")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="chatsReceived")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    private $target;
}
