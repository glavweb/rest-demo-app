<?php

/*
 * This file is part of the Glavweb RestDemoBundle package.
 *
 * (c) Andrey Nilov <nilov@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum as EnumAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Glavweb\RestBundle\Mapping\Annotation\Rest;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Article
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 * @package Glavweb\RestDemoBundle
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity
 * @Vich\Uploadable
 *
 * @Rest(
 *     methods={"list", "view", "post", "put", "patch", "delete"},
 *     enums={"type"},
 *     files={"image"},
 *     associations={
 *         "events"={"list", "post", "link", "unlink"}
 *     }
 * )
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @EnumAssert(entity="AppBundle\DBAL\Types\ArticleType")
     * @ORM\Column(name="type", type="ArticleType", length=255, options={"comment": "Type"})
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string")
     * @Assert\NotBlank
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var int
     *
     * @ORM\Column(name="count_events", type="integer", nullable=true)
     */
    private $countEvents;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_publish", type="boolean")
     */
    private $publish = false;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true, options={"comment": "Image"})
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="article_image", fileNameProperty="image")
     * @Assert\File(maxSize="4000000")
     * @Assert\Image(
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
     * @var \DateTime
     *
     * @ORM\Column(name="publish_at", type="datetime", nullable=true)
     */
    private $publishAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="articles")
     */
    private $events;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Article
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Article
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $countEvents
     */
    public function setCountEvents($countEvents)
    {
        $this->countEvents = $countEvents;
    }

    /**
     * @return int
     */
    public function getCountEvents()
    {
        return $this->countEvents;
    }

    /**
     * @param boolean $publish
     *
     * @return Article
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublish()
    {
        return $this->publish;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Article
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Article
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
     * @param \DateTime $publishAt
     *
     * @return Article
     */
    public function setPublishAt($publishAt)
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishAt()
    {
        return $this->publishAt;
    }

    /**
     * Add event
     *
     * @param Event $event
     *
     * @return Article
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param Event $event
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
