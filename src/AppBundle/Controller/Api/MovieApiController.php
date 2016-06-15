<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Glavweb\RestBundle\Controller\GlavwebRestController;
use Glavweb\RestBundle\Mapping\Annotation as RestExtra;
use Glavweb\RestBundle\Scope\ScopeFetcherInterface;
use Glavweb\DatagridBundle\Filter\Doctrine\Filter;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Movie;

/**
 * Class MovieApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieApiController extends GlavwebRestController
{
    /**
     * Returns collection of movies
     *
     * @ApiDoc(
     *     views={"default", "movie"},
     *     section="Movie API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie.schema.yml"}}
     *     }
     * )
     *
     * @Route("movies", name="api_movie_get_movies", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie/list.yml")
     * @RestExtra\Scope(name="list_short", path="movie/list_short.yml")
     *
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return Movie[]
     */
    public function getMoviesAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Movie::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('movie.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('name')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns movie
     *
     * @ApiDoc(
     *     views={"default", "movie"},
     *     section="Movie API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie.schema.yml"}}
     *     }
     * )
     *
     * @Route("movies/{movie}", name="api_movie_get_movie", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie/view.yml")
     *
     * @param Movie $movie
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getMovieAction(Movie $movie, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Movie::class)
            ->setDataSchema('movie.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $movie->getId()]);

        return $this->view($datagrid->getItem());
    }
}