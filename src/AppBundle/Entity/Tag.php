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

/**
 * Tag
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity
 *
 * @GlavwebSecurity\Access(
 *     name = "Tag",
 *     baseRole="ROLE_TAG_%s",
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view"}
 * )
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Tag ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, options={"comment": "Name"})
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Movie", mappedBy="tags")
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
     * Set name
     *
     * @param string $name
     *
     * @return Tag
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
     * Add movie
     *
     * @param Movie $movie
     *
     * @return Tag
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
