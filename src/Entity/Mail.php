<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 * @ORM\Table(name="mail")
 */
class Mail extends Message
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mailsSent")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mailsReceived")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    private $target;
}
