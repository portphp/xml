<?php

namespace Port\Xml;

use Port\Writer;
use Port\Writer\FlushableWriter;

/**
 * Write data to XML
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class XmlWriter implements Writer, FlushableWriter
{
    /**
     * @var \XmlWriter
     */
    protected $xmlWriter;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $rootElement;

    /**
     * @var string
     */
    protected $itemElement;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string|null
     */
    protected $encoding;

    /**
     * @param string $file
     */
    public function __construct(
        \XmlWriter $xmlWriter,
        $file,
        $rootElement = 'items',
        $itemElement = 'item',
        $version = '1.0',
        $encoding = null
    ) {
        $this->xmlWriter = $xmlWriter;
        $this->file = $file;
        $this->rootElement = $rootElement;
        $this->itemElement = $itemElement;
        $this->version = $version;
        $this->encoding = $encoding;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->xmlWriter->openUri($this->file);
        $this->xmlWriter->startDocument($this->version, $this->encoding);
        $this->xmlWriter->startElement($this->rootElement);
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->xmlWriter->startElement($this->itemElement);

        foreach ($item as $key => $value) {
            $this->xmlWriter->writeElement($key, $value);
        }

        $this->xmlWriter->endElement();
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        $this->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->xmlWriter->flush();
    }
}
