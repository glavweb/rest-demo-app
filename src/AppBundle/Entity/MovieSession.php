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

use Doctrine\ORM\Mapping as ORM;
use Glavweb\SecurityBundle\Mapping\Annotation as GlavwebSecurity;
use Glavweb\RestBundle\Mapping\Annotation as RestExtra;

/**
 * Session
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="movie_sessions")
 * @ORM\Entity
 *
 * @GlavwebSecurity\Access(
 *     name = "MovieSession",
 *     baseRole="ROLE_MOVIE_SESSION_%s",
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view"}
 * )
 */
class MovieSession
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Session ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", options={"comment": "Name"})
     */
    private $name;

    /**
     * @var Movie
     *
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="sessions")
     */
    private $movie;

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
     * @return MovieSession
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
     * Set movie
     *
     * @param Movie $movie
     *
     * @return MovieSession
     */
    public function setMovie(Movie $movie = null)
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
}
