<?php

namespace Ninfac\ContaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AclClasses
 *
 * @ORM\Table(name="acl_classes", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_69dd750638a36066", columns={"class_type"})})
 * @ORM\Entity
 */
class AclClasses
{
    /**
     * @var string
     *
     * @ORM\Column(name="class_type", type="string", length=200, nullable=false)
     */
    private $classType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="acl_classes_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;



    /**
     * Set classType
     *
     * @param string $classType
     *
     * @return AclClasses
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;

        return $this;
    }

    /**
     * Get classType
     *
     * @return string
     */
    public function getClassType()
    {
        return $this->classType;
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
}
