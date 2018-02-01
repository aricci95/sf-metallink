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
    protected $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mailsReceived")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    protected $target;

    /**
     * Gets the value of user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param User $user the user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of target.
     *
     * @return User
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the value of target.
     *
     * @param User $target the target
     *
     * @return self
     */
    public function setTarget(User $target)
    {
        $this->target = $target;

        return $this;
    }
}
