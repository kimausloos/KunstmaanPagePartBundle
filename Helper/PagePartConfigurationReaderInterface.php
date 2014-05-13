<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;

interface PagePartConfigurationReaderInterface
{
    /**
     * This will read the $name file and parse it to the PageTemplate
     *
     * @param string $name
     * @throws \Exception
     * @return PageTemplate
     */
    public function parse($name);

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page);

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page);
} 