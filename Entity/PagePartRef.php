<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * Reference between a page and a pagepart
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\PagePartBundle\Repository\PagePartRefRepository")
 * @ORM\Table(name="kuma_page_part_refs", indexes={@ORM\Index(name="idx_kuma_search", columns={"pageId", "pageEntityname", "context"})})
 * @ORM\HasLifecycleCallbacks()
 */
class PagePartRef
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $pageId;

    /**
     * @ORM\Column(type="string")
     */
    protected $pageEntityname;

    /**
     * @ORM\Column(type="string")
     */
    protected $context;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sequencenumber;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $pagePartId;

    /**
     * @ORM\Column(type="string")
     */
    protected $pagePartEntityname;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
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
     * @param integer $num
     *
     * @return PagePartRef
     */
    public function setId($num)
    {
        $this->id = $num;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param integer $id
     *
     * @return PagePartRef
     */
    public function setPageId($id)
    {
        $this->pageId = $id;

        return $this;
    }

    /**
     * Get pageEntityname
     *
     * @return string
     */
    public function getPageEntityname()
    {
        return $this->pageEntityname;
    }

    /**
     * Set pageEntityname
     *
     * @param string $pageEntityname
     *
     * @return PagePartRef
     */
    public function setPageEntityname($pageEntityname)
    {
        $this->pageEntityname = $pageEntityname;

        return $this;
    }

    /**
     * get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return PagePartRef
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get sequencenumber
     *
     * @return integer
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * Set sequencenumber
     *
     * @param integer $sequencenumber
     *
     * @return PagePartRef
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;

        return $this;
    }

    /**
     * Get pagePartId
     *
     * @return integer
     */
    public function getPagePartId()
    {
        return $this->pagePartId;
    }

    /**
     * Set pagePartId
     *
     * @param integer $pagePartId
     *
     * @return PagePartRef
     */
    public function setPagePartId($pagePartId)
    {
        $this->pagePartId = $pagePartId;

        return $this;
    }

    /**
     * Get pagePartEntityname
     *
     * @return string
     */
    public function getPagePartEntityname()
    {
        return $this->pagePartEntityname;
    }

    /**
     * Set pagePartEntityname
     *
     * @param string $pagePartEntityname
     *
     * @return PagePartRef
     */
    public function setPagePartEntityname($pagePartEntityname)
    {
        $this->pagePartEntityname = $pagePartEntityname;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return PagePartRef
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return PagePartRef
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "pagepartref in context " . $this->getContext();
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return \Kunstmaan\PagePartBundle\Helper\PagePartInterface
     */
    public function getPagePart(EntityManager $em)
    {
        return $em->getRepository($this->getPagePartEntityname())->find($this->getPagePartId());
    }
}
