<?php

namespace Debril\RssAtomBundle\Driver;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-26 at 00:32:41.
 */
class FileDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileDriver
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FileDriver();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Debril\RssAtomBundle\Driver\FileDriver::getResponse
     *
     * @todo   Implement testGetResponse().
     * @expectedException \Debril\RssAtomBundle\Exception\DriverUnreachableResourceException
     */
    public function testGetResponseException()
    {
        $url = dirname(__FILE__).'/../../Resources/dummy.rss';

        $this->object->getResponse($url, new \DateTime());
    }

    /**
     * @covers Debril\RssAtomBundle\Driver\FileDriver::getResponse
     *
     * @todo   Implement testGetResponse().
     */
    public function testGetResponse()
    {
        $url = dirname(__FILE__).'/../../Resources/sample-rss.xml';

        $response = $this->object->getResponse($url, new \DateTime());

        $this->assertTrue($response instanceof HttpDriverResponse);

        $this->assertInternalType('string', $response->getBody());
        $this->assertGreaterThan(0, strlen($response->getBody()));
    }
}
