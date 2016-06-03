<?php

namespace AppBundle\Controller\Api;

use Glavweb\DatagridBundle\Filter\Doctrine\Filter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Glavweb\RestBundle\Controller\GlavwebRestController;
use Glavweb\RestBundle\Mapping\Annotation as RestExtra;
use Glavweb\RestBundle\Scope\ScopeFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType as ArticleFormType;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType as EventFormType;
use AppBundle\DBAL\Types\ArticleType as ArticleEnumType;
use Glavweb\RestBundle\Form\FileType as FileFormType;

/**
 * Class ArticleApiController
 * @package AppBundle\Controller\Api
 *
 * @Rest\NamePrefix("api_article_")
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
     * @Security("is_granted('ROLE_ARTICLE_LIST')")
     *
     * @Rest\Route("articles", requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+",
     *     "_format": "json"
     * })
     *
     * @RestExtra\Scope(name="list", path="article/article.yml")
     *
     * @Rest\QueryParam(name="type", nullable=true, description="Type")
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     * @Rest\QueryParam(name="slug", nullable=true, description="Slug")
     * @Rest\QueryParam(name="body", nullable=true, description="Body")
     * @Rest\QueryParam(name="countEvents", nullable=true, description="Count events")
     * @Rest\QueryParam(name="publish", nullable=true, description="Publish")
     * @Rest\QueryParam(name="updatedAt", nullable=true, description="Updated at")
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
            ->setMaxResults($request->get('_limit'))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('article.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'article/article.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('type')
            ->addFilter('name')
            ->addFilter('slug')
            ->addFilter('body')
            ->addFilter('countEvents')
            ->addFilter('publish')
            ->addFilter('updatedAt')
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
     * @Security("is_granted('ROLE_ARTICLE_VIEW', article)")
     *
     * @Rest\Route("articles/{article}", requirements={
     *     "_scope":  "[\w,]+",
     *     "_format": "json"
     * })
     *
     * @RestExtra\Scope(name="list", path="article/article.yml")
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
            ->setDataSchema('article.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'article/article.yml'))
            ->addFilter('id', null, ['operator' => Filter::EQ])
        ;

        $datagrid = $datagridBuilder->build(['id' => $article->getId()]);

        return $this->view($datagrid->getItem());
    }

    /**
     * Create article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     input={"class"="AppBundle\Form\ArticleType", "name"=""},
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_CREATE')")
     *
     * @Rest\Route("articles", requirements={
     *     "_format": "json"
     * })
     *
     * @param Request $request A Symfony request
     * @return FormInterface|Response
     */
    public function postArticleAction(Request $request)
    {
        $formType = new ArticleFormType();
        $article = new Article();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'   => $request,
            'formType'  => $formType,
            'entity'    => $article,
            'onSuccess' => function($request, $form, Article $article, $response) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_article_get_article',
                        array('article' => $article->getId()),
                        true
                    )
                );
            }
        ));

        return $actionResponse->response;
    }

    /**
     * Update article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     input={"class"="AppBundle\Form\ArticleType", "name"=""},
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_EDIT', article)")
     *
     * @Rest\Route("articles/{article}", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Request $request
     * @return FormInterface|Response
     */
    public function putArticleAction(Article $article, Request $request)
    {
        $formType = new ArticleFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'    => $request,
            'formType'   => $formType,
            'entity'     => $article,
            'cleanForm'  => false
        ));

        return $actionResponse->response;
    }

    /**
     * Patch article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     input={"class"="AppBundle\Form\ArticleType", "name"=""},
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_EDIT', article)")
     *
     * @Rest\Route("articles/{article}", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Request $request
     * @return FormInterface|Response
     */
    public function patchArticleAction(Article $article, Request $request)
    {
        $formType = new ArticleFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'   => $request,
            'formType'  => $formType,
            'entity'    => $article,
            'cleanForm' => true
        ));

        return $actionResponse->response;
    }

    /**
     * Delete article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_DELETE', article)")
     *
     * @Rest\Route("articles/{article}", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @return Response
     */
    public function deleteArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Returns collection of events
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
     *         200 = {"class": null, "options": {"data_schema": "event.schema.yml"}}
     *     }
     * )
     *
     * @Security("is_granted('ROLE_EVENT_LIST')")
     *
     * @Rest\Route("articles/{article}/events", defaults={"_scope": "event_list"}, requirements={
     *     "_scope":  "[\w,]+",
     *     "_oprs":   "\d+",
     *     "_sort":   "ASC|DESC",
     *     "_offset": "\d+",
     *     "_limit":  "\d+",
     *     "_format": "json"
     * })
     *
     * @RestExtra\Scope(name="list", path="event/event.yml")
     *
     * @Rest\QueryParam(name="name", nullable=true, description="Name")
     *
     * @param Article $article
     * @param ParamFetcherInterface $paramFetcher
     * @param ScopeFetcherInterface $scopeFetcher
     * @param Request $request
     * @return Article[]
     */
    public function getArticleEventsAction(Article $article, ParamFetcherInterface $paramFetcher, ScopeFetcherInterface $scopeFetcher, Request $request)
    {
        // Define datagrid builder
        $datagridBuilder = $this->get('glavweb_datagrid.doctrine_datagrid_builder');
        $datagridBuilder
            ->setEntityClassName(Event::class)
            ->setFirstResult($request->get('_offset'))
            ->setMaxResults($request->get('_limit'))
            ->setOrderings($request->get('_sort'))
            ->setOperators($request->get('_oprs', []))
            ->setDataSchema('event.schema.yml', $scopeFetcher->getAvailable($request->get('_scope'), 'event/event.yml'))
        ;

        // Define filters
        $datagridBuilder
            ->addFilter('name')
        ;

        $datagrid = $datagridBuilder->build(array_merge($paramFetcher->all(), ['articles' => $article->getId()]));

        return $this->createListViewByDatagrid($datagrid);
    }

    /**
     * Create event
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     input={"class"="AppBundle\Form\EventType", "name"=""},
     *     statusCodes={
     *         201="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_EVENT_CREATE')")
     *
     * @Rest\Route("articles/{article}/events", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Request $request A Symfony request
     * @return FormInterface|Response
     */
    public function postArticleEventsAction(Article $article, Request $request)
    {
        $formType = new EventFormType();
        $event = new Event();
        $event->addArticle($article);
        $article->addEvent($event);

        $request->request->set('article', $article->getId());

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'   => $request,
            'formType'  => $formType,
            'entity'    => $event,
            'onSuccess' => function($request, $form, Event $event, $response) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'api_event_get_event',
                        array('event' => $event->getId()),
                        true
                    )
                );
            }
        ));

        return $actionResponse->response;
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
     * @Security("is_granted('ROLE_ARTICLE_VIEW')")
     *
     * @Rest\Route("articles/constant/type", requirements={
     *     "_format": "json"
     * })
     * @Rest\View()
     *
     * @return array
     */
    public function getConstantTypeAction()
    {
        return ArticleEnumType::getChoices();
    }

    /**
     * Upload image for article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     input={"class"="Glavweb\RestBundle\Form\FileType", "name"=""},
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_EDIT', article)")
     *
     * @Rest\Route("articles/{article}/file/image", defaults={"_scope": "article_view"}, requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Request $request
     * @return FormInterface|Response
     */
    public function postArticleFileImageAction(Article $article, Request $request)
    {
        $formType = new FileFormType();

        $restFormAction = $this->get('glavweb_rest.form_action');
        $actionResponse = $restFormAction->execute(array(
            'request'              => $request,
            'formType'             => $formType,
            'formOptions'          => ['property_path' => 'imageFile'],
            'entity'               => $article,
            'cleanForm'            => true
        ));

        return $actionResponse->response;
    }

    /**
     * Delete image for article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_ARTICLE_EDIT', article)")
     *
     * @Rest\Route("articles/{article}/file/image", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Request $request
     * @return FormInterface|Response
     */
    public function deleteArticleFileImageAction(Article $article, Request $request)
    {
        $uploadHandler = $this->get('vich_uploader.upload_handler');
        $uploadHandler->remove($article, 'imageFile');

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Link article to event
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_EVENT_CREATE')")
     *
     * @Rest\Route("articles/{article}/events/{event}", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Event $event
     * @return Response
     */
    public function linkArticleEventsAction(Article $article, Event $event)
    {
        $article->addEvent($event);
        $event->addArticle($article);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 204);
    }

    /**
     * Unlink event from article
     *
     * @ApiDoc(
     *     views={"default", "article"},
     *     section="Article API",
     *     statusCodes={
     *         204="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Security("is_granted('ROLE_EVENT_CREATE')")
     *
     * @Rest\Route("articles/{article}/events/{event}", requirements={
     *     "_format": "json"
     * })
     *
     * @param Article $article
     * @param Event $event
     * @return Response
     */
    public function unlinkArticleEventsAction(Article $article, Event $event)
    {
        $article->removeEvent($event);
        $event->removeArticle($article);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 204);
    }
}