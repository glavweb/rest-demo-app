<?php

namespace ExampleBundle\Controller\Api;

use ExampleBundle\Entity\Entity;
use Gos\Component\WebSocketClient\Exception\BadResponseException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class EntityController
 * @package ExampleBundle\Controller\Api
 */
class EntityController extends BaseRestController
{
    /**
     * Returns collection of entities
     *
     * @ApiDoc(
     *   section="Entity API"
     *   statusCodes={
     *     200="Returned when successful",
     *     500="Returned when an error has occurred"
     *   }
     * )
     *
     * @Security("is_granted('ROLE_ENTITY_LIST')")
     * @Route("entities.{_format}", name="api_entity_list", methods={"GET"}, defaults={"_format"="json"}, requirements={"_format"="json|xml"})
     *
     * @Rest\Route(requirements={"_format"="json|xml"})
     *
     * @Rest\QueryParam(name="name",    nullable=true, description="Name of entity")
     * @Rest\QueryParam(name="q",       nullable=true, description="Query of customer name, customer phone, street")
     * @Rest\QueryParam(name="_sort",   array=true, requirements="ASC|DESC", nullable=true, description="Sort (key is field, order is direction")
     * @Rest\QueryParam(name="_limit",  requirements="\d+", nullable=true, strict=true, description="Limit")
     * @Rest\QueryParam(name="_offset", requirements="\d+", nullable=true, strict=true, description="Offset")
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true, serializerGroups={"entity_list"})
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return Entity[]
     */
    public function getEntitiesAction(ParamFetcherInterface $paramFetcher)
    {
        $repository = $this->getRepository('OrderBundle:Order');
        $q = $paramFetcher->get('q');

        $fields = $paramFetcher->all();
        unset($fields['q']);

        return $this->matching($repository, $fields, function ($criteria) use ($q) {
            /** @var Criteria $criteria */
            $expr = $criteria->expr();

            if ($q) {
                $criteria->andWhere(
                    $expr->orX(
                        $expr->contains('name', $q),
                        $expr->contains('address', $q)
                    )
                );
            }
        });
    }

    /**
     * Returns entity
     *
     * @ApiDoc(
     *   section="Entity API",
     *   statusCodes={
     *     200="Returned when successful",
     *     500="Returned when an error has occurred"
     *   }
     * )
     *
     * @Security("is_granted('ROLE_ENTITY_VIEW', entity)")
     * @Route("entities/{entity}.{_format}", name="api_entity_list", methods={"GET"}, defaults={"_format"="json"}, requirements={"_format"="json|xml"})
     *
     * @Rest\Route(requirements={"_format"="json|xml"})
     * @Rest\View(serializerEnableMaxDepthChecks=true, serializerGroups={"entity_view"})
     *
     * @param Entity $entity
     * @return Entity
     */
    public function getEntityAction(Entity $entity)
    {
        return $entity;
    }
}
