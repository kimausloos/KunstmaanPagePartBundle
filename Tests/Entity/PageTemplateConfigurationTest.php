<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;

class PageTemplateConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageTemplateConfiguration
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PageTemplateConfiguration();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    private function getEntityManagerMock()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        return $em;
    }

    private function getRepositoryMock()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        return $repo;
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::getPageId
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::setPageId
     */
    public function testSetGetPageId()
    {
        $this->object->setPageId(123);
        $this->assertEquals(123, $this->object->getPageId());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::getPageEntityName
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::setPageEntityName
     */
    public function testSetGetPageEntityName()
    {
        $this->object->setPageEntityName('ABundle:AnEntity');
        $this->assertEquals('ABundle:AnEntity', $this->object->getPageEntityName());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::getPageTemplate
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::setPageTemplate
     */
    public function testSetGetPageTemplate()
    {
        $this->object->setPageTemplate('ATemplate');
        $this->assertEquals('ATemplate', $this->object->getPageTemplate());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration::getPage
     */
    public function testGetPage()
    {
        $em   = $this->getEntityManagerMock();
        $repo = $this->getRepositoryMock();
        $page = $this->getMock('Kunstmaan\NodeBundle\Entity\PageInterface');
        $this->object->setPageEntityName('ABundle:AnEntity')->setPageId(10);

        $em->expects($this->once())
            ->method('getRepository')
            ->with('ABundle:AnEntity')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('find')
            ->with(10)
            ->will($this->returnValue($page));

        $this->assertEquals($page, $this->object->getPage($em));
    }

}
 