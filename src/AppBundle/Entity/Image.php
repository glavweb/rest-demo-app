<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Glavweb\SecurityBundle\Mapping\Annotation as GlavwebSecurity;
use Glavweb\RestBundle\Mapping\Annotation as RestExtra;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use UserBundle\Entity\User;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Image
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="images")
 * @ORM\Entity
 * @Vich\Uploadable
 *
 * @GlavwebSecurity\Access(
 *     name = "Image",
 *     baseRole="ROLE_IMAGE_%s",
 *     additionalRoles={
 *         "Owner": {
 *             "condition": "{{alias}}.author = {{userId}}"
 *         }
 *     }
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view", "create", "update", "delete"},
 *     files={"image"}
 * )
 */
class Image
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Image ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", options={"comment": "Name"})
     * @Assert\NotBlank
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true, options={"comment": "Image"})
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="image", fileNameProperty="image")
     * @Assert\NotBlank(groups={"new"})
     * @Assert\File(maxSize="4000000")
     * @Assert\Image(
     *     minWidth = 120,
     *     maxWidth = 1200,
     *     minHeight = 120,
     *     maxHeight = 1200,
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/png", "image/gif"}
     * )
     *
     * @var File
     */
    private $imageFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at", nullable=true, options={"comment": "Updated at"})
     */
    private $updatedAt = null;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    private $author;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\MovieComment", mappedBy="images")
     */
    private $movieComments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movieComments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'n/a';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set image
     *
     * @param string $image
     *
     * @return Image
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param File $file
     */
    public function setImageFile(File $file = null)
    {
        $this->imageFile = $file;

        if ($file) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Image
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

    /**
     * Set author
     *
     * @param User $author
     *
     * @return Image
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add movieComment
     *
     * @param MovieComment $movieComment
     *
     * @return Image
     */
    public function addMovieComment(MovieComment $movieComment)
    {
        if (!$movieComment->getImages()->contains($this)) {
            $movieComment->addImage($this);
        }

        $this->movieComments[] = $movieComment;

        return $this;
    }

    /**
     * Remove movieComment
     *
     * @param MovieComment $movieComment
     */
    public function removeMovieComment(MovieComment $movieComment)
    {
        $movieComment->removeImage($this);
        $this->movieComments->removeElement($movieComment);
    }

    /**
     * Get movieComments
     *
     * @return ArrayCollection
     */
    public function getMovieComments()
    {
        return $this->movieComments;
    }
}
