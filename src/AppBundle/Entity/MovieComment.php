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
use UserBundle\Entity\User;

/**
 * MovieComment
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="movie_comments")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @GlavwebSecurity\Access(
 *     name = "MovieComment",
 *     baseRole="ROLE_MOVIE_COMMENT_%s",
 *     additionalRoles={
 *         "Owner": {
 *             "condition": "{{alias}}.author = {{userId}}"
 *         }
 *     }
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view", "create", "update", "delete"},
 *     associations={
 *         "images"={"link", "unlink"}
 *     }
 * )
 *
 */
class MovieComment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Movie Comment ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", options={"comment": "Body"})
     * @Assert\NotBlank
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_publish", type="boolean", options={"comment": "Is publish"})
     */
    private $publish = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", options={"comment": "Created At"})
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="movieComments")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    private $author;

    /**
     * @var Movie
     *
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="comments")
     * @ORM\JoinColumn(name="movie_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    private $movie;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="movieComments")
     */
    private $images;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBody() ? substr($this->getBody(), 0, '30') : 'n/a';
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
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
     * Set body
     *
     * @param string $body
     *
     * @return MovieComment
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
     * Set publish
     *
     * @param boolean $publish
     *
     * @return MovieComment
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return boolean
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * Is publish
     *
     * @return boolean
     */
    public function isPublish()
    {
        return $this->publish;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MovieComment
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
     * Set author
     *
     * @param User $author
     *
     * @return MovieComment
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
     * Set movie
     *
     * @param Movie $movie
     *
     * @return MovieComment
     */
    public function setMovie(Movie $movie)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get movie
     *
     * @return Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return MovieComment
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }
}
