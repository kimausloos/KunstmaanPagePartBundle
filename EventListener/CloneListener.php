<?php

namespace Kunstmaan\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\Service\PagePartServiceInterface;
use Kunstmaan\PagePartBundle\Service\PageTemplateServiceInterface;

/**
 * This event will make sure pageparts are being copied when deepClone is done on an entity implementing hasPagePartsInterface
 */
class CloneListener
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PagePartServiceInterface
     */
    private $pagePartService;

    /**
     * @var PageTemplateServiceInterface
     */
    private $pageTemplateService;

    /**
     * @param EntityManager                $em The entity manager
     * @param PagePartServiceInterface     $pagePartService
     * @param PageTemplateServiceInterface $pageTemplateService
     */
    public function __construct(
        EntityManager $em,
        PagePartServiceInterface $pagePartService,
        PageTemplateServiceInterface $pageTemplateService
    ) {
        $this->em                  = $em;
        $this->pagePartService     = $pagePartService;
        $this->pageTemplateService = $pageTemplateService;
    }

    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof HasPagePartsInterface) {
            $clonedEntity = $event->getClonedEntity();

            $contexts = $this->pagePartService->getPagePartContexts($originalEntity);
            foreach ($contexts as $context) {
                $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts(
                    $this->em,
                    $originalEntity,
                    $clonedEntity,
                    $context
                );
            }
        }

        if ($originalEntity instanceof HasPageTemplateInterface) {
            $clonedEntity = $event->getClonedEntity();

            $newPageTemplateConfiguration = clone $this->pageTemplateService->findOrCreateFor($originalEntity);
            $newPageTemplateConfiguration->setId(null);
            $newPageTemplateConfiguration->setPageId($clonedEntity->getId());
            $this->pageTemplateService->save($newPageTemplateConfiguration);
        }
    }
}
