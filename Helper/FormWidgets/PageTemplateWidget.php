<?php

namespace Kunstmaan\PagePartBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Kunstmaan\PagePartBundle\Service\PagePartServiceInterface;
use Kunstmaan\PagePartBundle\Service\PageTemplateServiceInterface;

/**
 * PageTemplateWidget
 */
class PageTemplateWidget extends FormWidget
{
    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $widgets = array();

    /**
     * @var PageTemplate[]
     */
    protected $pageTemplates = array();

    /**
     * @var AbstractPagePartAdminConfigurator[]
     */
    protected $pagePartAdminConfigurators = array();

    /**
     * @var PageTemplateConfiguration
     */
    protected $pageTemplateConfiguration;

    /**
     * @param HasPageTemplateInterface     $page                       The page
     * @param Request                      $request                    The request
     * @param PageTemplateServiceInterface $pageTemplateService        The page template manager
     * @param PagePartServiceInterface     $pagePartService            The page part manager
     * @param FormFactoryInterface         $formFactory                The form factory
     */
    public function __construct(
        HasPageTemplateInterface $page,
        Request $request,
        PageTemplateServiceInterface $pageTemplateService,
        PagePartServiceInterface $pagePartService,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct();

        $this->page                       = $page;
        $this->request                    = $request;
        $this->pagePartAdminConfigurators = $pagePartService->getPagePartAdminConfigurators($page);
        $this->pageTemplateConfiguration  = $pageTemplateService->findOrCreateFor($page);
        $this->pageTemplates              = $pageTemplateService->getPageTemplatesFor($page);

        foreach ($this->getPageTemplate()->getRows() as $row) {
            foreach ($row->getRegions() as $region) {
                $pagePartAdminConfiguration = null;
                foreach ($this->pagePartAdminConfigurators as $pagePartAdminConfigurator) {
                    if ($pagePartAdminConfigurator->getContext() == $region->getName()) {
                        $pagePartAdminConfiguration = $pagePartAdminConfigurator;
                    }
                }
                if ($pagePartAdminConfiguration != null) {
                    $pagePartAdmin = $pagePartService->getPagePartAdmin(
                        $pagePartAdminConfiguration,
                        $page,
                        null
                    );
                    $pagePartWidget                    = new PagePartWidget($this->request, $formFactory, $pagePartAdmin);
                    $this->widgets[$region->getName()] = $pagePartWidget;
                }
            }
        }
    }

    /**
     * @return PageTemplate
     */
    public function getPageTemplate()
    {
        return $this->pageTemplates[$this->pageTemplateConfiguration->getPageTemplate()];
    }

    /**
     * @return PageTemplate
     */
    public function getPageTemplates()
    {
        return $this->pageTemplates;
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        foreach ($this->widgets as $widget) {
            $widget->buildForm($builder);
        }
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $configurationName = $request->get('pagetemplate_template');
        $this->pageTemplateConfiguration->setPageTemplate($configurationName);
        foreach ($this->widgets as $widget) {
            $widget->bindRequest($request);
        }
    }

    /**
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        $em->persist($this->pageTemplateConfiguration);
        foreach ($this->widgets as $widget) {
            $widget->persist($em);
        }
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = array();

        foreach ($this->widgets as $widget) {
            $errors = array_merge($errors, $widget->getFormErrors($formView));
        }

        return $errors;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanPagePartBundle:FormWidgets\PageTemplateWidget:widget.html.twig';
    }

    /**
     * @param string $name
     *
     * @return PagePartAdmin
     */
    public function getFormWidget($name)
    {
        if (array_key_exists($name, $this->widgets)) {
            return $this->widgets[$name];
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = array();

        return $params;
    }

}
