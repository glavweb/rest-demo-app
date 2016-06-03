<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class ArticleType
 * @package AppBundle\DBAL\Types
 */
class ArticleType extends AbstractEnumType
{
    const NEWS         = 'news';
    const PHOTO_REPORT = 'photo_report';
    const ARTICLE      = 'article';

    /**
     * @var array
     */
    protected static $choices = array(
        self::NEWS         => 'News',
        self::PHOTO_REPORT => 'Photo report',
        self::ARTICLE      => 'Article'
    );
}