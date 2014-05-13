<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\PagePartBundle\Service\PageTemplateServiceInterface;

/**
 * PagePartTwigExtension
 */
class PageTemplateTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var PageTemplateServiceInterface
     */
    private $pageTemplateService;

    /**
     * @param PageTemplateServiceInterface $pageTemplateService The page template manager
     */
    public function __construct(PageTemplateServiceInterface $pageTemplateService)
    {
        $this->pageTemplateService = $pageTemplateService;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'render_pagetemplate' => new \Twig_Function_Method($this, 'renderPageTemplate', array(
                    'needs_context' => true,
                    'is_safe'       => array('html')
                )),
            'getpagetemplate'     => new \Twig_Function_Method($this, 'getPageTemplate')
        );
    }

    /**
     * @param array $twigContext             The twig context
     * @param HasPageTemplateInterface $page The page
     * @param array $parameters              Some extra parameters
     *
     * @return string
     */
    public function renderPageTemplate(array $twigContext, HasPageTemplateInterface $page, array $parameters = array())
    {
        $pageTemplates = $this->pageTemplateService->getPageTemplatesFor($page);

        /* @var $pageTemplate PageTemplate */
        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];
        $template     = $this->environment->loadTemplate($pageTemplate->getTemplate());

        return $template->render($twigContext);
    }

    /**
     * @param HasPageTemplateInterface $page The page
     *
     * @return string
     */
    public function getPageTemplate(HasPageTemplateInterface $page)
    {
        $pageTemplateConfiguration = $this->pageTemplateService->findOrCreateFor($page);

        return $pageTemplateConfiguration->getPageTemplate();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagetemplate_twig_extension';
    }

}
