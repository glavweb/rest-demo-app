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
use AppBundle\Entity\MovieComment;
use AppBundle\Form\EditMovieCommentType as EditMovieCommentFormType;
use AppBundle\Entity\Image;

/**
 * Class MovieCommentApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieCommentApiController extends GlavwebRestController
{
    /**
     * Returns collection of movie comments
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_comment.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-comments", name="api_movie_comment_get_movie_comments", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_comment/list.yml")
     *
     * @Rest\QueryParam(name="body", nullable=true, description="Body")
     * @Rest\QueryParam(name="publish", nullable=true, description="Publish")
     * @Rest\QueryParam(name="createdAt", nullable=true, description="Created at")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return MovieComment[]
     */
    public function getMovieCommentsAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieComment::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('movie_comment.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_comment/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('body')
            ->addFilter('publish')
            ->addFilter('createdAt')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns movie comment
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "movie_comment.schema.yml"}}
     *     }
     * )
     *
     * @Route("movie-comments/{movieComment}", name="api_movie_comment_get_movie_comment", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="movie_comment/view.yml")
     *
     * @param MovieComment $movieComment
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getMovieCommentAction(MovieComment $movieComment, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(MovieComment::class)
            ->setDataSchema('movie_comment.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'movie_comment/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $movieComment->getId()]);

        return $this->view($datagrid->getItem());
    }

    /**
     * Create movie comment
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     input={"class"="AppBundle\Form\CreateMovieCommentType", "name"=""},
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_MOVIE_COMMENT_CREATE')")
     *
     * @Route("movie-comments", name="api_movie_comment_create_movie_comment", defaults={"_format": "json"}, methods={"POST"})
     *
     * @param Request $request A Symfony request
     * @return FormInterface|Response
     */
    public function createMovieCommentAction(Request $request)
    {
        $formType = $this->get('create_movie_comment_form');
        $movieComment = new MovieComment();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'   => $request,
            'formType'  => $formType,
            'entity'    => $movieComment,
            'onSuccess' => function($request, $form, MovieComment $movieComment, $response) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_movie_comment_get_movie_comment',
                        array('movieComment' => $movieComment->getId()),
                        true
                    )
                );
            }
        ));

        return $actionResponse->response;
    }

    /**
     * Update movie comment
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     input={"class"="AppBundle\Form\EditMovieCommentType", "name"=""},
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_MOVIE_COMMENT_EDIT', movieComment)")
     *
     * @Route("movie-comments/{movieComment}", name="api_movie_comment_put_movie_comment", defaults={"_format": "json"}, methods={"PUT"})
     * @Route("movie-comments/{movieComment}", name="api_movie_comment_patch_movie_comment", defaults={"_format": "json", "isPatch": true}, methods={"PATCH"})
     *
     * @param MovieComment $movieComment
     * @param Request $request
     * @param bool    $isPatch
     * @return FormInterface|Response
     */
    public function updateMovieCommentAction(MovieComment $movieComment, Request $request, $isPatch = false)
    {
        $formType = new EditMovieCommentFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'    => $request,
            'formType'   => $formType,
            'entity'     => $movieComment,
            'cleanForm'  => $isPatch
        ));

        return $actionResponse->response;
    }

    /**
     * Delete movie comment
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_MOVIE_COMMENT_DELETE', movieComment)")
     *
     * @Route("movie-comments/{movieComment}", name="api_movie_comment_delete_movie_comment", defaults={"_format": "json"}, methods={"DELETE"})
     *
     * @param MovieComment $movieComment
     * @return Response
     */
    public function deleteMovieCommentAction(MovieComment $movieComment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($movieComment);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Link movie comment to image
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_MOVIE_COMMENT_EDIT', movieComment) and is_granted('ROLE_IMAGE_VIEW', image)")
     *
     * @Route("movie-comments/{movieComment}/images/{image}", name="api_movie_comment_link_movie_comment_images", defaults={"_format": "json"}, methods={"LINK"})
     *
     * @param MovieComment $movieComment
     * @param Image $image
     * @return Response
     */
    public function linkMovieCommentImagesAction(MovieComment $movieComment, Image $image)
    {
        if ($movieComment->getImages()->contains($image)) {
            return new Response('', 204);
        }

        $movieComment->addImage($image);
        $image->addMovieComment($movieComment);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 204);
    }

    /**
     * Unlink image from movie comment
     *
     * @ApiDoc(
     *     views={"default", "movie-comment"},
     *     section="MovieComment API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_MOVIE_COMMENT_EDIT', movieComment)")
     *
     * @Route("movie-comments/{movieComment}/images/{image}", name="api_movie_comment_unlink_movie_comment_images", defaults={"_format": "json"}, methods={"UNLINK"})
     *
     * @param MovieComment $movieComment
     * @param Image $image
     * @return Response
     */
    public function unlinkMovieCommentImagesAction(MovieComment $movieComment, Image $image)
    {
        if (!$movieComment->getImages()->contains($image)) {
            return new Response('', 204);
        }

        $movieComment->removeImage($image);
        $image->removeMovieComment($movieComment);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 204);
    }
}