<?php

namespace ExampleBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class ExampleType
 * @package ExampleBundle\DBAL\Types
 */
class ExampleType extends AbstractEnumType
{
    const STATUS_ONE = 'status_one';

    protected static $choices = array(
        self::STATUS_ONE => 'Status one',
    );
}