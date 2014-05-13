<?php

namespace Kunstmaan\PagePartBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * PageTemplateConfigurationRepository
 */
class PageTemplateConfigurationRepository extends EntityRepository
{

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findFor(HasPageTemplateInterface $page)
    {
        return $this->findOneBy(array('pageId' => $page->getId(), 'pageEntityName' => ClassLookup::getClass($page)));
    }

    /**
     * @param PageTemplateConfiguration $configuration
     *
     * @return PageTemplateConfiguration
     */
    public function save(PageTemplateConfiguration $configuration)
    {
        $em = $this->getEntityManager();
        $em->persist($configuration);

        return $configuration;
    }

}
