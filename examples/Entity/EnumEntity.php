<?php

namespace ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum as EnumAssert;

/**
 * EnumEntity
 *
 * @ORM\Table(name="enum_entities")
 * @ORM\Entity
 */
class EnumEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @EnumAssert(entity="ExampleBundle\DBAL\Types\ExampleType")
     * @ORM\Column(name="type", type="ExampleType", nullable=false)
     */
    private $type;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return EnumEntity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
