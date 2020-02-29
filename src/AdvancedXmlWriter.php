<?php

namespace Port\Xml;

use Port\Writer;
use Port\Writer\FlushableWriter;

/**
 * This writer allows you to write more complex XML from arrays than the default XmlWriter.
 *
 * The main differences are that nested arrays can be proceed and XML attributes are supported. 
 * If the key of an array value starts with '@' the value will be used as a XML attribute.
 */
class AdvancedXmlWriter implements Writer, FlushableWriter
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
     * @var array<string, string>
     */
    protected $rootElementAttributes;

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
        $rootElementAttributes = [],
        $itemElement = 'item',
        $version = '1.0',
        $encoding = null
    ) {
        $this->xmlWriter = $xmlWriter;
        $this->file = $file;
        $this->rootElement = $rootElement;
        $this->rootElementAttributes = $rootElementAttributes;
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

        foreach($this->rootElementAttributes as $key => $value) {
            $this->xmlWriter->writeAttribute($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->writeXmlItem($item, $this->itemElement);
    }

    /**
     * Handle XML write with attributes support.
     *
     * @param array $item
     * @param $elementName
     */
    protected function writeXmlItem(array $item, $elementName)
    {
        $this->xmlWriter->startElement($elementName);

        $this->writeAttributes($item);

        foreach ($item as $key => $value) {
            if (substr($key, 0, 1) === '@') {
                continue;
            }

            if (is_array($value)) {
                $this->writeXmlItem($value, $key);
            } else {
                $this->xmlWriter->writeElement($key, $value);
            }
        }

        $this->xmlWriter->endElement();
    }

    /**
     * Write attributes of item.
     *
     * @param array $item
     */
    protected function writeAttributes(array $item)
    {
        foreach ($item as $key => $value) {
            if (substr($key, 0, 1) !== '@') {
                continue;
            }

            $this->xmlWriter->writeAttribute(
                substr($key, 1),
                $value
            );
        }
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
