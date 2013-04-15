<?php

namespace Kunstmaan\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\PageTemplateConfigurationReader;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * PagePartTwigExtension
 */
class PageTemplateTwigExtension extends \Twig_Extension
{

    protected $em;

    /**
     * @var KernelInterface::
     */
    protected $kernel;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;
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
            'render_pagetemplate'  => new \Twig_Function_Method($this, 'renderPageTemplate', array('needs_context' => true, 'is_safe' => array('html'))),
            'getpagetemplate'  => new \Twig_Function_Method($this, 'getPageTemplate')
        );
    }

    /**
     * @param $twigContext
     * @param HasPagePartsInterface $page       The page
     * @param string                $context    The pagepart context
     * @param array                 $parameters Some extra parameters
     *
     * @return string
     */
    public function renderPageTemplate($twigContext, HasPageTemplateInterface $page, array $parameters = array())
    {
        $pageTemplateConfigurationReader = new PageTemplateConfigurationReader($this->kernel);
        $pageTemplates = array();
        foreach ($page->getPageTemplates() as $pageTemplate) {
            $pt = null;
            if (is_string($pageTemplate)) {
                $pt = $pageTemplateConfigurationReader->parse($pageTemplate);
            } else if (is_object($pageTemplate) && $pageTemplate instanceof PageTemplate) {
                $pt = $pageTemplate;
            } else {
                throw new \Exception("don't know how to handle the pageTemplate " . get_class($pageTemplate));
            }
            $pageTemplates[$pt->getName()] = $pt;
        }

        /* @var $pageTemplate PageTemplate */
        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $this->environment->loadTemplate($pageTemplate->getTemplate());
        /* @var $entityRepository PagePartRefRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');

        return $template->render($twigContext);
    }

    /**
     * @param HasPageTemplateInterface $page The page
     *
     * @return string
     */
    public function getPageTemplate(HasPageTemplateInterface $page)
    {
        /**@var $entityRepository PageTemplateConfigurationRepository */
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PageTemplateConfiguration');
        $pageTemplateConfiguration = $entityRepository->findFor($page);

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
