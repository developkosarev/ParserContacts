<?php

namespace AppBundle\Entity\Parsers;

use Doctrine\ORM\Mapping as ORM;

/**
 * Investor
 *
 * @ORM\Table(name="parsers_investor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Parsers\InvestorRepository")
 */
class Investor
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="ParserId", type="integer")
     */
    private $parserId;

    /**
     * @var string
     *
     * @ORM\Column(name="Phone", type="string", length=30)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Specialization", type="string", length=255)
     */
    private $specialization;

    /**
     * @var string
     *
     * @ORM\Column(name="Address", type="string", length=255)
     */
    private $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CreatedAt", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parserId
     *
     * @param integer $parserId
     *
     * @return Investor
     */
    public function setParserId($parserId)
    {
        $this->parserId = $parserId;

        return $this;
    }

    /**
     * Get parserId
     *
     * @return int
     */
    public function getParserId()
    {
        return $this->parserId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Investor
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Investor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set specialization
     *
     * @param string $specialization
     *
     * @return Investor
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * Get specialization
     *
     * @return string
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Investor
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Investor
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

