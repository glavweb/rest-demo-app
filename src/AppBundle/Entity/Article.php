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
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum as EnumAssert;
use Glavweb\SecurityBundle\Mapping\Annotation as GlavwebSecurity;
use Glavweb\RestBundle\Mapping\Annotation as RestExtra;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity
 *
 * @GlavwebSecurity\Access(
 *     name = "Article",
 *     baseRole="ROLE_ARTICLE_%s",
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view"},
 *     enums={"type"}
 * )
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Article ID"})
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
     * @ORM\Column(name="name", type="string", options={"comment": "Name"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true, options={"comment": "Body"})
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
     * @ORM\Column(name="publish_at", type="datetime", nullable=true, options={"comment": "Publish At"})
     */
    private $publishAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Movie", inversedBy="articles")
     */
    private $movies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->movies = new ArrayCollection();
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
     * Set publish
     *
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
     * Get publish
     *
     * @return boolean
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * Set publishAt
     *
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
     * Get publishAt
     *
     * @return \DateTime
     */
    public function getPublishAt()
    {
        return $this->publishAt;
    }

    /**
     * Add movie
     *
     * @param Movie $movie
     *
     * @return Article
     */
    public function addMovie(Movie $movie)
    {
        $this->movies[] = $movie;

        return $this;
    }

    /**
     * Remove movie
     *
     * @param Movie $movie
     */
    public function removeMovie(Movie $movie)
    {
        $this->movies->removeElement($movie);
    }

    /**
     * Get movies
     *
     * @return ArrayCollection
     */
    public function getMovies()
    {
        return $this->movies;
    }
}
