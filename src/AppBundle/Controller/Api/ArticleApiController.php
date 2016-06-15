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
use AppBundle\Entity\Article;
use AppBundle\DBAL\Types\ArticleType as ArticleEnumType;

/**
 * Class ArticleApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArticleApiController extends GlavwebRestController
{
    /**
     * Returns collection of articles
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "article.schema.yml"}}
     *     }
     * )
     *
     * @Route("articles", name="api_article_get_articles", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="article/list.yml")
     *
     * @Rest\QueryParam(name="type", nullable=true, description="Type")
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     * @Rest\QueryParam(name="body", nullable=true, description="Body")
     * @Rest\QueryParam(name="publish", nullable=true, description="Publish")
     * @Rest\QueryParam(name="publishAt", nullable=true, description="Publish at")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return Article[]
     */
    public function getArticlesAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Article::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('article.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'article/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('type')
            ->addFilter('name')
            ->addFilter('body')
            ->addFilter('publish')
            ->addFilter('publishAt')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "article.schema.yml"}}
     *     }
     * )
     *
     * @Route("articles/{article}", name="api_article_get_article", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="article/view.yml")
     *
     * @param Article $article
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getArticleAction(Article $article, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Article::class)
            ->setDataSchema('article.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'article/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $article->getId()]);

        return $this->view($datagrid->getItem());
    }

    /**
     * Returns types of article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     }
     * )
     *
     * @Route("articles/constant/type", name="api_article_get_constant_type", defaults={"_format": "json"}, methods={"GET"})
     * @Rest\View()
     *
     * @return array
     */
    public function getConstantTypeAction()
    {
        return ArticleEnumType::getChoices();
    }
}