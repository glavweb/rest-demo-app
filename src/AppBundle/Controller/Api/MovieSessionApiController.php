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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
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
use AppBundle\Entity\MovieSession;

/**
 * Class MovieSessionApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieSessionApiController extends GlavwebRestController
{
    /**
     * Returns collection of movie sessions
     *
     * @ApiDoc(
     *     views={"default", "movie-session"},
     *     section="MovieSession API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_session.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-sessions", name="api_movie_session_get_movie_sessions", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_session/list.yml")
     *
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return MovieSession[]
     */
    public function getMovieSessionsAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieSession::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('movie_session.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_session/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('name')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns movie session
     *
     * @ApiDoc(
     *     views={"default", "movie-session"},
     *     section="MovieSession API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_session.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-sessions/{movieSession}", name="api_movie_session_get_movie_session", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_session/view.yml")
     *
     * @param MovieSession $movieSession
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getMovieSessionAction(MovieSession $movieSession, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieSession::class)
            ->setDataSchema('movie_session.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_session/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $movieSession->getId()]);

        return $this->view($datagrid->getItem());
    }
}