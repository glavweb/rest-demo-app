<?php

namespace ExampleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entity
 *
 * @ORM\Table(name="entities")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"entity_list", "entity_view"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"entity_list", "entity_view"})
     */
    private $address;

    /**
     * @var Entity
     *
     * @ORM\ManyToOne(targetEntity="ExampleBundle\Entity\Entity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     * @JMS\MaxDepth(depth=1)
     * @JMS\Groups({"entity_view"})
     */
    private $manyToOne;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ExampleBundle\Entity\Entity")
     * @ORM\OrderBy({"id" = "ASC"})
     * @JMS\Expose
     * @JMS\MaxDepth(depth=2)
     * @JMS\Groups({"entity_view"})
     */
    private $oneToMany;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
