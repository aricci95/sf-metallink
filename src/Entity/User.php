<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    const GENDER_MALE   = 1;
    const GENDER_FEMALE = 2;

    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;

    const PICTURE_ONLINE  = 'online.gif';
    const PICTURE_OFFLINE = 'offline.png';

    const ONLINE_TIME_THRESHOLD = 30;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="gender", type="smallint", nullable=false)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_connexion_date", type="datetime", nullable=true)
     */
    private $lastConnexionDate;

    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(name="is_tattooed", type="boolean", nullable=true)
     */
    private $isTattooed;

    /**
     * @var int
     *
     * @ORM\Column(name="is_pierced", type="boolean", nullable=true)
     */
    private $isPierced;

    /**
     * @var Picture
     *
     * @ORM\OneToMany(targetEntity="Picture", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @var Link
     *
     *  @ORM\OneToMany(targetEntity="Link", mappedBy="user")
     */
    private $linksSent;

    /**
     * @var Link
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="target")
     */
    private $linksReceived;
    
    /**
     * @var Mail
     *
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="user")
     */
    private $mailsSent;
    
    /**
     * @var Mail
     *
     * @ORM\OneToMany(targetEntity="Mail", mappedBy="target")
     */
    private $mailsReceived;

    /**
     * @var Chat
     *
     * @ORM\OneToMany(targetEntity="Chat", mappedBy="user")
     */
    private $chatsSent;
    
    /**
     * @var Chat
     *
     * @ORM\OneToMany(targetEntity="Chat", mappedBy="target")
     */
    private $chatsReceived;

    /**
     * @ORM\ManyToMany(targetEntity="Band", mappedBy="users")
     */
    private $bands;

    public function __construct()
    {
        $this->linksSent         = new ArrayCollection();
        $this->linksReceived     = new ArrayCollection();
        $this->mailsSent         = new ArrayCollection();
        $this->mailsReceived     = new ArrayCollection();
        $this->chatsSent         = new ArrayCollection();
        $this->chatsReceived     = new ArrayCollection();
        $this->pictures          = new ArrayCollection();
        $this->bands             = new ArrayCollection();
    }

    public function getDefaultPicture()
    {
        foreach ($this->pictures as $picture) {
            if ($picture->isDefault()) {
                return $picture;
            }
        }

        if ($this->pictures && $this->pictures[0]) {
            return $this->pictures[0];
        }

        return new Picture();
    }

    public function getPictures()
    {
        return $this->pictures;
    }

    public function isLinked(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_ACCEPTED;
    }

    public function isBlacklisted(User $user)
    {
        return $this->getLink($user)->getStatus() == Link::STATUS_BLACKLISTED;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAge()
    {
        if (!$this->birthdate) {
            return null;
        }

        return floor((time() - $this->birthdate->getTimestamp()) / 31556926);
    }

    /**
     * Gets the value of gender.
     *
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets the value of gender.
     *
     * @param int $gender the gender
     *
     * @return self
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Gets the value of birthdate.
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Sets the value of birthdate.
     *
     * @param \DateTime $birthdate the birthdate
     *
     * @return self
     */
    public function setBirthdate(\DateTime $birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Gets the value of job.
     *
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Sets the value of job.
     *
     * @param string $job the job
     *
     * @return self
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Gets the value of shortDescription.
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Sets the value of shortDescription.
     *
     * @param string $shortDescription the short description
     *
     * @return self
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Sets the value of weight.
     *
     * @param int $weight the weight
     *
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Gets the value of height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the value of height.
     *
     * @param int $height the height
     *
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Gets the value of isTattooed.
     *
     * @return smallint
     */
    public function isTattooed()
    {
        return $this->isTattooed;
    }

    /**
     * Sets the value of isTattooed.
     *
     * @param smallint $isTattooed the is tattooed
     *
     * @return self
     */
    public function setIsTattooed($isTattooed)
    {
        $this->isTattooed = $isTattooed;

        return $this;
    }

    /**
     * Gets the value of isPierced.
     *
     * @return smallint
     */
    public function isPierced()
    {
        return $this->isPierced;
    }

    /**
     * Sets the value of isPierced.
     *
     * @param smallint $isPierced the is pierced
     *
     * @return self
     */
    public function setIsPierced($isPierced)
    {
        $this->isPierced = $isPierced;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return int
     */
    public function getStatus()
    {
        if (!$this->lastConnexionDate) {
            return false;
        }

        $lastConnexionInterval = (new \DateTime())->diff($this->lastConnexionDate);

        return (!$lastConnexionInterval->h && $lastConnexionInterval->i <= self::ONLINE_TIME_THRESHOLD);
    }

    public function getStatusPicture()
    {
        return $this->getStatus() ? self::PICTURE_ONLINE : self::PICTURE_OFFLINE;
    }

    /**
     * Gets the value of lastConnexionDate.
     *
     * @return \DateTime
     */
    public function getLastConnexionDate()
    {
        return $this->lastConnexionDate;
    }

    public function getLastConnexionDelay()
    {
        return "Il y a 10 jours";
    }

    /**
     * Sets the value of lastConnexionDate.
     *
     * @param \DateTime $lastConnexionDate the last connexion date
     *
     * @return self
     */
    public function setLastConnexionDate(\DateTime $lastConnexionDate)
    {
        $this->lastConnexionDate = $lastConnexionDate;

        return $this;
    }

    /**
     * Gets the value of linksSent.
     *
     * @return Link
     */
    public function getLinksSent()
    {
        return $this->linksSent;
    }

    /**
     * @param Link $link
     */
    public function addLinkSent(Link $link)
    {
        if (!$this->linksSent->contains($link)) {
            $link->setUser($this);
            $this->linksSent->add($link);
        }
    }

    /**
     * @param Link $link
     */
    public function addPicture(Picture $picture)
    {
        if (!$this->pictures->contains($picture)) {
            $picture->setUser($this);
            $this->pictures->add($picture);
        }
    }

    /**
     * @param Link $link
     */
    public function removePicture(Picture $picture)
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->remove($picture);
        }
    }

    /**
     * Gets the value of linksReceived.
     *
     * @return Link
     */
    public function getLinksReceived()
    {
        return $this->linksReceived;
    }

    public function getLink(User $target)
    {
        if ($target == $this) {
            return null;
        }

        foreach ($this->linksSent as $link) {
            if ($link->getTarget()->getId() == $target->getId()) {
                return $link;
            }
        }

        foreach ($this->linksReceived as $link) {
            if ($link->getTarget()->getId() == $this->id) {
                return $link;
            }
        }

        $link = new Link();

        return $link
            ->setUser($this)
            ->setTarget($target);
    }

    /**
     * @param Band $band
     */
    public function addBand(Band $band)
    {
        if (!$this->bands->contains($band)) {
            $this->bands->add($band);
        }
    }

    /**
     * @param Band $band
     */
    public function removeBand(Band $band)
    {
        if ($this->bands->contains($band)) {
            $this->bands->removeElement($band);
        }
    }

    /**
     * Gets the value of bands.
     *
     * @return mixed
     */
    public function getBands()
    {
        return $this->bands;
    }
}
