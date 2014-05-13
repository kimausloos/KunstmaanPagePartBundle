<?php

namespace Kunstmaan\PagePartBundle\Service;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReaderInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PagePartService implements PagePartServiceInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var PagePartConfigurationReaderInterface $pagePartConfigReader */
    private $pagePartConfigReader;

    public function __construct(
        ContainerInterface $container,
        PagePartConfigurationReaderInterface $pagePartConfigReader
    ) {
        $this->container = $container;
        $this->pagePartConfigReader = $pagePartConfigReader;
    }

    /**
     * @param HasPagePartsInterface $page
     *
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page)
    {
        return $this->pagePartConfigReader->getPagePartAdminConfigurators($page);
    }

    /**
     * @param AbstractPagePartAdminConfigurator $configurator
     * @param HasPagePartsInterface             $page
     * @param null                              $context
     *
     * @return PagePartAdmin
     */
    public function getPagePartAdmin(
        AbstractPagePartAdminConfigurator $configurator,
        HasPagePartsInterface $page,
        $context = null
    ) {
        // PagePartAdmin still needs refactoring!
        $em = $this->container->get('doctrine.orm.entity_manager');

        return new PagePartAdmin($configurator, $em, $page, $context, $this->container);
    }

    /**
     * @param HasPagePartsInterface $page
     *
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page)
    {
        return $this->pagePartConfigReader->getPagePartContexts($page);
    }
}