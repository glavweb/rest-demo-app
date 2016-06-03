<?php

namespace ExampleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * UploadableEntity
 *
 * @ORM\Table(name="uploadable_entities")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class UploadableEntity
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
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt = null;

    /**
     * @Vich\UploadableField(mapping="entity_image", fileNameProperty="image")
     * @Assert\File(maxSize="4000000")
     * @Assert\Image(
     *     minWidth = 880,
     *     maxWidth = 880,
     *     minHeight = 400,
     *     maxHeight = 400,
     *     mimeTypes = {"image/jpeg", "image/jpg", "image/png", "image/gif"}
     * )
     *
     * @var File
     */
    private $imageFile;

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImageFile(File $file = null)
    {
        $this->imageFile = $file;

        if ($file) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

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
     * Set image
     *
     * @param string $image
     * @return WorkBlock
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return WorkBlock
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
