<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 * @ORM\Table(
 *     name="link",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"user_id", "target_id"})
 *     }
 *  )
 */
class Link
{
    const STATUS_NONE        = 0;
    const STATUS_PENDING     = 1;
    const STATUS_ACCEPTED    = 2;
    const STATUS_BLACKLISTED = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="linksSent")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="linksReceived")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    private $target;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status    = self::STATUS_NONE;
    }

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

    /**
     * Gets the value of status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param int $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getLinkedUser(User $user)
    {
        if ($this->user != $user) {
            return $this->user;
        }

        return $this->target;
    }

    /**
     * Sets the value of createdAt.
     *
     * @param \DateTime $createdAt the created at
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->status == self::STATUS_ACCEPTED;
    }

    public function isBlacklisted()
    {
        return $this->status == self::STATUS_BLACKLISTED;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
