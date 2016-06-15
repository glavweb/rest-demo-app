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
use AppBundle\Entity\MovieGroup;

/**
 * Class MovieGroupApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieGroupApiController extends GlavwebRestController
{
    /**
     * Returns collection of movie groups
     *
     * @ApiDoc(
     *     views={"default", "movie-group"},
     *     section="MovieGroup API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_group.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-groups", name="api_movie_group_get_movie_groups", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_group/list.yml")
     *
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return MovieGroup[]
     */
    public function getMovieGroupsAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieGroup::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('movie_group.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_group/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('name')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns movie group
     *
     * @ApiDoc(
     *     views={"default", "movie-group"},
     *     section="MovieGroup API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_group.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-groups/{movieGroup}", name="api_movie_group_get_movie_group", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_group/view.yml")
     *
     * @param MovieGroup $movieGroup
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getMovieGroupAction(MovieGroup $movieGroup, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieGroup::class)
            ->setDataSchema('movie_group.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_group/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $movieGroup->getId()]);

        return $this->view($datagrid->getItem());
    }
}