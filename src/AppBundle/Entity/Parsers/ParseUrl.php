<?php

namespace AppBundle\Entity\Parsers;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParseUrl
 *
 * @ORM\Table(name="parsers_parse_url")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Parsers\ParseUrlRepository")
 */
class ParseUrl
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
     * @var int
     *
     * @ORM\Column(name="CrawlerId", type="integer", nullable=true)
     */
    private $crawlerId;

    /**
     * @var string
     *
     * @ORM\Column(name="CrawlerName", type="string", length=100, nullable=true)
     */
    private $crawlerName;

    /**
     * @var int
     *
     * @ORM\Column(name="Status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="FileName", type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="Url", type="string", length=2048)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="InvestorId", type="integer", nullable=true)
     */
    private $investorId;


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
     * @return ParseUrl
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
     * Set crawlerId
     *
     * @param integer $crawlerId
     *
     * @return ParseUrl
     */
    public function setCrawlerId($crawlerId)
    {
        $this->crawlerId = $crawlerId;

        return $this;
    }

    /**
     * Get crawlerId
     *
     * @return int
     */
    public function getCrawlerId()
    {
        return $this->crawlerId;
    }

    /**
     * Set crawlerName
     *
     * @param string $crawlerName
     *
     * @return ParseUrl
     */
    public function setCrawlerName($crawlerName)
    {
        $this->crawlerName = $crawlerName;

        return $this;
    }

    /**
     * Get crawlerName
     *
     * @return string
     */
    public function getCrawlerName()
    {
        return $this->crawlerName;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ParseUrl
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return ParseUrl
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return ParseUrl
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set investorId
     *
     * @param integer $investorId
     *
     * @return ParseUrl
     */
    public function setInvestorId($investorId)
    {
        $this->investorId = $investorId;

        return $this;
    }

    /**
     * Get investorId
     *
     * @return int
     */
    public function getInvestorId()
    {
        return $this->investorId;
    }
}

