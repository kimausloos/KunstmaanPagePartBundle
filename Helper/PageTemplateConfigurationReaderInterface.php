<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;

interface PageTemplateConfigurationReaderInterface
{
    /**
     * @param string $name
     *
     * @return PageTemplate
     */
    public function parse($name);

    /**
     * @param HasPageTemplateInterface $page
     *
     * @return array
     */
    public function getPageTemplates(HasPageTemplateInterface $page);
}