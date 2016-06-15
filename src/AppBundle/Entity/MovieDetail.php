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
 * MovieDetail
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 *
 * @ORM\Table(name="movie_details")
 * @ORM\Entity
 *
 * @GlavwebSecurity\Access(
 *     name = "MovieDetail",
 *     baseRole="ROLE_MOVIE_DETAIL_%s",
 * )
 *
 * @RestExtra\Rest(
 *     methods={"list", "view"}
 * )
 */
class MovieDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"comment": "Movie Detail ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", options={"comment": "Body"})
     */
    private $body;

    /**
     * @var Movie
     *
     * @ORM\OneToOne(targetEntity="Movie", mappedBy="detail")
     */
    private $movie;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getBody() ? substr($this->getBody(), 0, '30') : 'n/a';
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
     * @return MovieDetail
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
     * Set movie
     *
     * @param Movie $movie
     *
     * @return MovieDetail
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
