<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Kunstmaan\AdminBundle\Helper\FormWidgets\ListWidget;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\Tab;
use Kunstmaan\NodeBundle\Event\AdaptFormEvent;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PagePartWidget;
use Kunstmaan\PagePartBundle\Helper\FormWidgets\PageTemplateWidget;
use Kunstmaan\PagePartBundle\Service\PagePartServiceInterface;
use Kunstmaan\PagePartBundle\Service\PageTemplateServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * NodeListener
 */
class NodeListener
{

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var PageTemplateServiceInterface
     */
    private $pageTemplateService;

    /**
     * @var PagePartServiceInterface
     */
    private $pagePartService;

    /**
     * @param FormFactoryInterface         $formFactory         The form factory
     * @param PagePartServiceInterface     $pagePartService     The page part manager
     * @param PageTemplateServiceInterface $pageTemplateService The page template manager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        PagePartServiceInterface $pagePartService,
        pageTemplateServiceInterface $pageTemplateService
    ) {
        $this->formFactory         = $formFactory;
        $this->pagePartService     = $pagePartService;
        $this->pageTemplateService = $pageTemplateService;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page    = $event->getPage();
        $tabPane = $event->getTabPane();

        $pagePartAdminConfigurators = $this->pagePartService->getPagePartAdminConfigurators($page);
        if ($page instanceof HasPageTemplateInterface) {
            $this->addPageTemplateWidget($event, $page, $tabPane);
        } elseif ($page instanceof HasPagePartsInterface) {
            $this->addPagePartWidgets($event, $pagePartAdminConfigurators, $tabPane);
        }
    }

    /**
     * @param AdaptFormEvent $event
     * @param                $page
     * @param                $tabPane
     */
    private function addPageTemplateWidget(AdaptFormEvent $event, $page, $tabPane)
    {
        $pageTemplateWidget = new PageTemplateWidget(
            $page,
            $event->getRequest(),
            $this->pageTemplateService,
            $this->pagePartService,
            $this->formFactory
        );
        /* @var Tab $propertiesTab */
        $propertiesTab = $tabPane->getTabByTitle('Properties');
        if (!is_null($propertiesTab)) {
            $propertiesWidget = $propertiesTab->getWidget();
            $tabPane->removeTab($propertiesTab);
            $tabPane->addTab(new Tab('Content', new ListWidget(array($propertiesWidget, $pageTemplateWidget))), 0);
        } else {
            $tabPane->addTab(new Tab('Content', $pageTemplateWidget), 0);
        }
    }

    /**
     * @param AdaptFormEvent $event
     * @param                $pagePartAdminConfigurators
     * @param                $tabPane
     */
    private function addPagePartWidgets(AdaptFormEvent $event, $pagePartAdminConfigurators, $tabPane)
    {
        foreach ($pagePartAdminConfigurators as $index => $pagePartAdminConfiguration) {
            $pagePartWidget = new PagePartWidget(
                $event->getRequest(),
                $this->formFactory,
                $this->pagePartAdminFactory);
            if ($index == 0) {
                /* @var Tab $propertiesTab */
                $propertiesTab = $tabPane->getTabByTitle('Properties');

                if (!is_null($propertiesTab)) {
                    $propertiesWidget = $propertiesTab->getWidget();
                    $tabPane->removeTab($propertiesTab);
                    $tabPane->addTab(
                        new Tab(
                            $pagePartAdminConfiguration->getName(),
                            new ListWidget(array(
                                $propertiesWidget,
                                $pagePartWidget
                            ))),
                        0
                    );

                    continue;
                }
            }
            $tabPane->addTab(
                new Tab(
                    $pagePartAdminConfiguration->getName(),
                    $pagePartWidget
                ),
                sizeof($tabPane->getTabs())
            );
        }
    }

}
