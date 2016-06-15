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
use AppBundle\Entity\Image;
use AppBundle\Form\ImageType as ImageFormType;
use Glavweb\RestBundle\Form\FileType as FileFormType;

/**
 * Class ImageApiController
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ImageApiController extends GlavwebRestController
{
    /**
     * Returns collection of images
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     statusCodes={
     *         200="Returned when successful",
     *         206="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "image.schema.yml"}}
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_LIST')")
     *
     * @Route("images", name="api_image_get_images", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="image/list.yml")
     *
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     * @Rest\QueryParam(name="updatedAt", nullable=true, description="Updated at")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return Image[]
     */
    public function getImagesAction(ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Image::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults(min($request->get('_limit', 100), 1000))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('image.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'image/list.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('name')
            ->addFilter('updatedAt')
        ;

        $datagrid = $datagridBuilder->build($paramFetcher->all());

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Returns image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred"
     *     },
     *     responseMap={
     *         200 = {"class": null, "options": {"data_schema": "image.schema.yml"}}
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_VIEW', image)")
     *
     * @Route("images/{image}", name="api_image_get_image", requirements={
     *     "_scope":  "[\w,]+"
     * }, defaults={"_format": "json"}, methods={"GET"})
     *
     * @RestExtra\Scope(name="list", path="image/view.yml")
     *
     * @param Image $image
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return View
     */
    public function getImageAction(Image $image, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Image::class)
            ->setDataSchema('image.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'image/view.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $image->getId()]);

        return $this->view($datagrid->getItem());
    }

    /**
     * Create image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     input={"class"="AppBundle\Form\ImageType", "name"=""},
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_CREATE')")
     *
     * @Route("images", name="api_image_create_image", defaults={"_format": "json"}, methods={"POST"})
     *
     * @param Request $request A Symfony request
     * @return FormInterface|Response
     */
    public function createImageAction(Request $request)
    {
        $formType = new ImageFormType();
        $image = new Image();
        $image->setAuthor($this->getUser());

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'   => $request,
            'formType'  => $formType,
            'entity'    => $image,
            'onSuccess' => function($request, $form, Image $image, $response) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_image_get_image',
                        array('image' => $image->getId()),
                        true
                    )
                );
            }
        ));

        return $actionResponse->response;
    }

    /**
     * Update image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     input={"class"="AppBundle\Form\ImageType", "name"="", "options"={"isNew":false}},
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_EDIT', image)")
     *
     * @Route("images/{image}", name="api_image_put_image", defaults={"_format": "json"}, methods={"PUT"})
     * @Route("images/{image}", name="api_image_patch_image", defaults={"_format": "json", "isPatch": true}, methods={"PATCH"})
     *
     * @param Image $image
     * @param Request $request
     * @param bool    $isPatch
     * @return FormInterface|Response
     */
    public function updateImageAction(Image $image, Request $request, $isPatch = false)
    {
        $formType = new ImageFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'    => $request,
            'formType'   => $formType,
            'formOptions' => ['isNew' => false],
            'entity'     => $image,
            'cleanForm'  => $isPatch
        ));

        return $actionResponse->response;
    }

    /**
     * Delete image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_DELETE', image)")
     *
     * @Route("images/{image}", name="api_image_delete_image", defaults={"_format": "json"}, methods={"DELETE"})
     *
     * @param Image $image
     * @return Response
     */
    public function deleteImageAction(Image $image)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Upload image for image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     input={"class"="Glavweb\RestBundle\Form\FileType", "name"=""},
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_EDIT', image)")
     *
     * @Route("images/{image}/file/image", name="api_image_upload_image_file_image", defaults={"_format": "json"}, methods={"POST"})
     *
     * @param Image $image
     * @param Request $request
     * @return FormInterface|Response
     */
    public function uploadImageFileImageAction(Image $image, Request $request)
    {
        $formType = new FileFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'     => $request,
            'formType'    => $formType,
            'formOptions' => ['property_path' => 'imageFile'],
            'entity'      => $image,
            'cleanForm'   => true
        ));

        return $actionResponse->response;
    }

    /**
     * Delete image for image
     *
     * @ApiDoc(
     *     views={"default", "image"},
     *     section="Image API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_IMAGE_EDIT', image)")
     *
     * @Route("images/{image}/file/image", name="api_image_delete_image_file_image", defaults={"_format": "json"}, methods={"DELETE"})
     *
     * @param Image $image
     * @return FormInterface|Response
     */
    public function deleteImageFileImageAction(Image $image)
    {
        $uploadHandler = $this->get('vich_uploader.upload_handler');
        $uploadHandler->remove($image, 'imageFile');

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response('', 204);
    }
}