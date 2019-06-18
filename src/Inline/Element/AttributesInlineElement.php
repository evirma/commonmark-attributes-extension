<?php

namespace Evirma\CommonMark\Inline\Element;

use League\CommonMark\Inline\Element\AbstractStringContainer;

class AttributesInlineElement extends AbstractStringContainer
{
    public $attributes;
    public $block;

    public function __construct(array $attributes, $block)
    {
        $this->attributes = $attributes;
        $this->block = (bool) $block;
        parent::__construct('', ['delim' => true]);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function isBlock()
    {
        return (bool) $this->block;
    }
}
