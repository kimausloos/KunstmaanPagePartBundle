<?php

namespace Kunstmaan\PagePartBundle\Service;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;

interface PagePartServiceInterface
{
    /**
     * @param HasPagePartsInterface $page
     *
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page);

    /**
     * @param AbstractPagePartAdminConfigurator $configurator
     * @param HasPagePartsInterface             $page
     * @param mixed                             $context
     *
     * @return PagePartAdmin
     */
    public function getPagePartAdmin(
        AbstractPagePartAdminConfigurator $configurator,
        HasPagePartsInterface $page,
        $context = null
    );

    /**
     * @param HasPagePartsInterface $page
     *
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page);
}