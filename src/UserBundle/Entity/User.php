<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User
 * @package UserBundle\Entity
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "User ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", nullable=true, options={"comment": "Last name"})
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", nullable=true, options={"comment": "First name"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", nullable=true, options={"comment": "Avatar file path"})
     */
    private $avatar;

    /**
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatar")
     * @Assert\File(maxSize="4000000")
     * @Assert\Image(
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/png", "image/gif"}
     * )
     *
     * @var File
     */
    private $avatarFile;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", nullable=true, options={"comment": "Token for API"})
     */
    private $apiToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="api_token_expire_at", type="datetime", nullable=true, options={"comment": "Time when API token will be expired"})
     */
    private $apiTokenExpireAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"comment": "Created at"})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"comment": "Updated at"})
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Group", inversedBy="users")
     */
    protected $groups;

    /**
     * @return string
     */
    public function __toString()
    {
        $fullName = $this->getFirstname() . ' ' . $this->getLastname();

        return trim($fullName) ? $fullName : $this->getUsername();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param File|UploadedFile $file
     */
    public function setAvatarFile(File $file = null)
    {
        $this->avatarFile = $file;

        if ($file) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @return File
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set apiTokenExpireAt
     *
     * @param \DateTime $apiTokenExpireAt
     *
     * @return User
     */
    public function setApiTokenExpireAt($apiTokenExpireAt)
    {
        $this->apiTokenExpireAt = $apiTokenExpireAt;

        return $this;
    }

    /**
     * Get apiTokenExpireAt
     *
     * @return \DateTime
     */
    public function getApiTokenExpireAt()
    {
        return $this->apiTokenExpireAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
