<?php

namespace Kunstmaan\PagePartBundle\Service;

use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;

interface PageTemplateServiceInterface
{
    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findFor(HasPageTemplateInterface $page);

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findOrCreateFor(HasPageTemplateInterface $page);

    /**
     * @param HasPageTemplateInterface $page
     * @param PageTemplate             $defaultPageTemplate
     *
     * @return PageTemplateConfiguration
     */
    public function createFor(HasPageTemplateInterface $page, PageTemplate $defaultPageTemplate);

    /**
     * @param PageTemplateConfiguration $configuration
     *
     * @return PageTemplateConfiguration
     */
    public function save(PageTemplateConfiguration $configuration);

    /**
     * @return PageTemplateConfigurationReaderInterface
     */
    public function getReader();

    /**
     * @param PageTemplateConfigurationReaderInterface $reader
     *
     * @return PageTemplateService
     */
    public function setReader(PageTemplateConfigurationReaderInterface $reader);

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return array of PageTemplate
     */
    public function getPageTemplatesFor(HasPageTemplateInterface $page);
}