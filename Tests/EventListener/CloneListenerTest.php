<?php

namespace Kunstmaan\PagePartBundle\Tests\EventListener;

use Kunstmaan\PagePartBundle\EventListener\CloneListener;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * CloneListenerTest
 */
class CloneListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Doctrine\ORM\EntityRepository
     */
    protected $repo;

    /**
     * @var CloneListener
     */
    protected $object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $pagePartService;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageTemplateService;

    /**
     * Sets up the fixture.
     *
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::__construct
     */
    protected function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(array('copyPageParts'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with('KunstmaanPagePartBundle:PagePartRef')
            ->will($this->returnValue($this->repo));

        $this->pagePartService = $this->getMock('Kunstmaan\PagePartBundle\Service\PagePartServiceInterface');
        $this->pageTemplateService = $this->getMock('Kunstmaan\PagePartBundle\Service\PageTemplateServiceInterface');

        $this->object = new CloneListener($this->em, $this->pagePartService, $this->pageTemplateService);
    }

    /**
     * Tears down the fixture.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::postDeepCloneAndSave
     */
    public function testClonePagePart()
    {
        $entity = $this->getMock('Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface');

        $clone = clone $entity;

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $this->pagePartService->expects($this->once())
            ->method('getPagePartContexts')
            ->with($this->identicalTo($entity))
            ->will($this->returnValue(array('main')));

        $event = new DeepCloneAndSaveEvent($entity, $clone);
        $this->object->postDeepCloneAndSave($event);
    }

    /**
     * @covers Kunstmaan\PagePartBundle\EventListener\CloneListener::postDeepCloneAndSave
     */
    public function testClonePageTemplate()
    {
        $entity = $this->getMockBuilder('Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface')
            ->setMethods(array('getId', 'getPageTemplates', 'getPagePartAdminConfigurations'))
            ->getMock();

        $clone = clone $entity;

        $clone->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));

        $this->repo->expects($this->once())
            ->method('copyPageParts')
            ->with($this->em, $entity, $clone, 'main');

        $this->pagePartService->expects($this->once())
            ->method('getPagePartContexts')
            ->with($this->identicalTo($entity))
            ->will($this->returnValue(array('main')));

        $configuration = new PageTemplateConfiguration();
        $configuration
            ->setId(1)
            ->setPageId(1);

        $this->pageTemplateService->expects($this->once())
            ->method('findOrCreateFor')
            ->with($this->identicalTo($entity))
            ->will($this->returnValue($configuration));

        $newConfiguration = clone $configuration;
        $newConfiguration
            ->setId(null)
            ->setPageId($clone->getId());

        $this->pageTemplateService->expects($this->once())
            ->method('save')
            ->with($newConfiguration);

        $event = new DeepCloneAndSaveEvent($entity, $clone);
        $this->object->postDeepCloneAndSave($event);
    }
}
