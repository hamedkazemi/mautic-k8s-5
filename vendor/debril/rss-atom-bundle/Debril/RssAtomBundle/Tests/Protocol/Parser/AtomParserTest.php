<?php

namespace Debril\RssAtomBundle\Protocol\Parser;

use Debril\RssAtomBundle\Protocol\Filter\ModifiedSince;
use Debril\RssAtomBundle\Tests\Protocol\ParserAbstract;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-27 at 00:26:35.
 */
class AtomParserTest extends ParserAbstract
{
    /**
     * @var AtomParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AtomParser();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::parse
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::canHandle
     * @expectedException \Debril\RssAtomBundle\Exception\ParserException
     */
    public function testCannotHandle()
    {
        $file = dirname(__FILE__).'/../../../Resources/sample-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertFalse($this->object->canHandle($xmlBody));
        $filters = array(new ModifiedSince(new \DateTime()));
        $this->object->parse($xmlBody, new FeedContent(), $filters);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::canHandle
     */
    public function testCanHandle()
    {
        $file = dirname(__FILE__).'/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertTrue($this->object->canHandle($xmlBody));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::checkBodyStructure
     * @expectedException \Debril\RssAtomBundle\Exception\ParserException
     */
    public function testParseError()
    {
        $file = dirname(__FILE__).'/../../../Resources/truncated-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $filters = array(new ModifiedSince(new \DateTime()));
        $this->object->parse($xmlBody, new FeedContent(), $filters);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::parse
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::parseBody
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::parseContent
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::detectLink
     */
    public function testParse()
    {
        $file = dirname(__FILE__).'/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat('Y-m-d', '2002-10-10');
        $filters = array(new ModifiedSince($date));
        $feed = $this->object->parse($xmlBody, new FeedContent(), $filters);

        $this->assertInstanceOf('Debril\RssAtomBundle\Protocol\FeedInInterface', $feed);

        $this->assertNotNull($feed->getPublicId(), 'feed->getId() should not return an empty value');
        $this->assertGreaterThan(0, $feed->getItemsCount());
        $this->assertInstanceOf('\DateTime', $feed->getLastModified());
        $this->assertNotNull($feed->getLink());
        $this->assertInternalType('string', $feed->getLink());
        $this->assertInternalType('string', $feed->getDescription());
        $this->assertInternalType('string', $feed->getTitle());
        $this->assertNotNull($feed->getDescription());
        $this->assertNotNull($feed->getTitle());

        $item = current($feed->getItems());
        $this->assertInternalType('string', $item->getAuthor());
        $this->assertEquals('John Doe', $item->getAuthor());

        $medias = $item->getMedias();
        $count = 0;
        foreach ($medias as $media) {
            $this->assertInstanceOf('Debril\RssAtomBundle\Protocol\Parser\Media', $media);
            ++$count;
        }

        $this->assertEquals(1, $count);

        $categories = $item->getCategories();
        $this->assertCount(2, $categories);
        $this->assertInstanceOf('Debril\RssAtomBundle\Protocol\Parser\Category', $categories[0]);
        $this->assertEquals('Category1', $categories[0]->getName());
        $this->assertEquals('Category2', $categories[1]->getName());
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::setDateFormats
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::__construct
     * @dataProvider getDefaultFormats
     */
    public function testSetDateFormats($default)
    {
        $this->object->setdateFormats($default);
        $this->assertEquals($default, $this->readAttribute($this->object, 'dateFormats'));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::guessDateFormat
     * @dataProvider getDefaultFormats
     * @expectedException \Debril\RssAtomBundle\Exception\ParserException
     */
    public function testGuessDateFormatException($default)
    {
        $this->object->setdateFormats($default);

        $date = '2003-13T18:30:02Z';
        $this->object->guessDateFormat($date);
    }

    /**
     *
     */
    public function testHtmlContent()
    {
        $file = dirname(__FILE__).'/../../../Resources/sample-atom-html.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat('Y-m-d', '2002-10-10');
        $filters = array(new ModifiedSince($date));
        $feed = $this->object->parse($xmlBody, new FeedContent(), $filters);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedInInterface", $feed);
        $item = current($feed->getItems());

        $this->assertTrue(strlen($item->getDescription()) > 0);
    }

    /**
     *
     */
    public function testHtmlSummary()
    {
        $file = dirname(__FILE__).'/../../../Resources/sample-atom-summary.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat('Y-m-d', '2002-10-10');
        $filters = array(new ModifiedSince($date));
        $feed = $this->object->parse($xmlBody, new FeedContent(), $filters);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedInInterface", $feed);
        $item = current($feed->getItems());

        $expected = '<div xmlns="http://www.w3.org/1999/xhtml"><p>sample text</p></div>';
        $this->assertEquals($expected, $item->getSummary());
    }
}
