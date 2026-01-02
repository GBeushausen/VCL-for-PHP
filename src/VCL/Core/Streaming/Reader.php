<?php

declare(strict_types=1);

namespace VCL\Core\Streaming;

use VCL\Core\Component;

/**
 * A class for reading components from an XML stream.
 *
 * Inherits from Filer and provides a method to start the read process
 * which will create the components stored on the XML file and assign
 * all properties.
 */
class Reader extends Filer
{
    /**
     * Reads a component and all its children from a stream.
     *
     * @param Component $root Root component to read into
     * @param string $stream XML stream content
     */
    public function readRootComponent(Component $root, string $stream): void
    {
        $this->Root = $root;

        xml_parse($this->_xmlparser, $stream);

        $this->_root->ControlState = 0;
    }
}
