<?php

namespace Kunstmaan\PagePartBundle\Service;

use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\PagePartBundle\Repository\PageTemplateConfigurationRepository;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class PageTemplateService implements PageTemplateServiceInterface
{
    /** @var PageTemplateConfigurationRepository $repository */
    private $repository;

    /** @var PageTemplateConfigurationReaderInterface $reader */
    private $reader;

    /**
     * @param $repository
     * @param $reader
     */
    public function __construct(PageTemplateConfigurationRepository $repository, PageTemplateConfigurationReaderInterface $reader)
    {
        $this->repository = $repository;
        $this->reader = $reader;
    }

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findFor(HasPageTemplateInterface $page)
    {
        return $this->repository->findFor($page);
    }

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findOrCreateFor(HasPageTemplateInterface $page)
    {
        $pageTemplateConfiguration = $this->findFor($page);

        if (is_null($pageTemplateConfiguration)) {
            $pageTemplates = $this->pageTemplates = $this->reader->getPageTemplates($page);
            $names = array_keys($pageTemplates);
            $defaultPageTemplate = $pageTemplates[$names[0]];

            $pageTemplateConfiguration = $this->createFor($page, $defaultPageTemplate);
        }

        return $pageTemplateConfiguration;
    }

    /**
     * @param HasPageTemplateInterface $page
     * @param PageTemplate             $defaultPageTemplate
     *
     * @return PageTemplateConfiguration
     */
    public function createFor(HasPageTemplateInterface $page, PageTemplate $defaultPageTemplate)
    {
        $pageTemplateConfiguration = new PageTemplateConfiguration();
        $pageTemplateConfiguration->setPageId($page->getId());
        $pageTemplateConfiguration->setPageEntityName(ClassLookup::getClass($page));
        $pageTemplateConfiguration->setPageTemplate($defaultPageTemplate->getName());

        return $pageTemplateConfiguration;
    }

    /**
     * @param PageTemplateConfiguration $configuration
     *
     * @return PageTemplateConfiguration
     */
    public function save(PageTemplateConfiguration $configuration)
    {
        return $this->repository->save($configuration);
    }

    /**
     * @return PageTemplateConfigurationReaderInterface
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param PageTemplateConfigurationReaderInterface $reader
     *
     * @return PageTemplateService
     */
    public function setReader(PageTemplateConfigurationReaderInterface $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return array of PageTemplate
     */
    public function getPageTemplatesFor(HasPageTemplateInterface $page)
    {
        return $this->getReader()->getPageTemplates($page);
    }
}