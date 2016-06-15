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

/**
 * Movie
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="movies")
 * @ORM\Entity
 *
 * @GlavwebSecurity\Access(
 *     name = "Movie",
 *     baseRole="ROLE_MOVIE_%s",
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view"}
 * )
 *
 */
class Movie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Movie ID"})
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
     * @var MovieGroup
     *
     * @ORM\ManyToOne(targetEntity="MovieGroup", inversedBy="movies")
     */
    private $group;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="movies", cascade={"persist", "remove"})
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="movies")
     */
    private $articles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MovieSession", mappedBy="movie")
     */
    private $sessions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MovieComment", mappedBy="movie")
     */
    private $comments;

    /**
     * @var MovieDetail
     *
     * @ORM\OneToOne(targetEntity="MovieDetail", inversedBy="movie", cascade={"persist", "remove"})
     */
    private $detail;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags     = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * @return Movie
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
     * Set group
     *
     * @param MovieGroup $group
     *
     * @return Movie
     */
    public function setGroup(MovieGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return MovieGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Movie
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add article
     *
     * @param Article $article
     *
     * @return Movie
     */
    public function addArticle(Article $article)
    {
        $article->addMovie($this);
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param Article $article
     */
    public function removeArticle(Article $article)
    {
        $article->removeMovie($this);
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add session
     *
     * @param MovieSession $session
     *
     * @return Movie
     */
    public function addSession(MovieSession $session)
    {
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param MovieSession $session
     */
    public function removeSession(MovieSession $session)
    {
        $this->sessions->removeElement($session);
    }

    /**
     * Get sessions
     *
     * @return ArrayCollection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Add comment
     *
     * @param MovieComment $comment
     *
     * @return Movie
     */
    public function addComment(MovieComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param MovieComment $comment
     */
    public function removeComment(MovieComment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set detail
     *
     * @param MovieDetail $detail
     *
     * @return Movie
     */
    public function setDetail(MovieDetail $detail = null)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return MovieDetail
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
