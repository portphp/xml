<?php

namespace spec\Port\Xml;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XmlWriterSpec extends ObjectBehavior
{
    function let(\XmlWriter $xmlWriter)
    {
        $this->beConstructedWith($xmlWriter, 'file', 'elements', 'element', '1.0', 'UTF-8');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Port\Xml\XmlWriter');
    }

    function it_is_a_writer()
    {
        $this->shouldImplement('Port\Writer');
        $this->shouldImplement('Port\Writer\FlushableWriter');
    }

    function it_writes_an_item(\XmlWriter $xmlWriter)
    {
        $xmlWriter->openUri('file')->shouldBeCalled();
        $xmlWriter->startDocument('1.0', 'UTF-8')->shouldBeCalled();
        $xmlWriter->startElement('elements')->shouldBeCalled();
        $xmlWriter->startElement('element')->shouldBeCalledTimes(2);
        $xmlWriter->writeElement('key1', 'value1')->shouldBeCalled();
        $xmlWriter->writeElement('key2', 'value2')->shouldBeCalled();
        $xmlWriter->endElement()->shouldBeCalledTimes(3);
        $xmlWriter->endDocument()->shouldBeCalled();
        $xmlWriter->flush()->shouldBeCalled();

        $this->prepare();

        $this->writeItem(['key1' => 'value1']);
        $this->writeItem(['key2' => 'value2']);

        $this->finish();
    }
}
