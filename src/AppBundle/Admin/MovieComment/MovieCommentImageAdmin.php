<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Admin\MovieComment;

use AppBundle\Admin\ImageAdmin;
use AppBundle\Admin\MovieCommentAdmin;
use AppBundle\Entity\Image;
use AppBundle\Entity\Movie;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class MovieCommentImageAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieCommentImageAdmin extends ImageAdmin
{
    /**
     * The related field reflection, ie if OrderElement is linked to Order,
     * then the $parentReflectionProperty must be the ReflectionProperty of
     * the order (OrderElement::$order)
     *
     * @var \ReflectionProperty $parentReflectionProperty
     */
    protected $parentAssociationMapping = 'movieComments';

    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'movie-comment-image';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'movie_comment_image';

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        /** @var Image $image */
        $image = $this->getSubject();

        $image->addMovieComment($this->getParent()->getSubject());
    }
}
