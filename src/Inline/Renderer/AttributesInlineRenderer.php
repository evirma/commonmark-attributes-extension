<?php

namespace Evirma\CommonMark\Inline\Renderer;

use InvalidArgumentException;
use Evirma\CommonMark\Inline\Element\AttributesInlineElement;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

final class AttributesInlineRenderer implements InlineRendererInterface
{
    /**
     * @param AbstractInline           $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof AttributesInlineElement)) {
            throw new InvalidArgumentException('Incompatible inline type: '.get_class($inline));
        }
        $inline->detach();

        return '';
    }
}
