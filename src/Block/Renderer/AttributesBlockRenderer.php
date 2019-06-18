<?php

namespace Evirma\CommonMark\Block\Renderer;

use Evirma\CommonMark\Block\Element\AttributesBlockElement;
use InvalidArgumentException;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;

final class AttributesBlockRenderer implements BlockRendererInterface
{
    /**
     * @param AbstractBlock            $block
     * @param ElementRendererInterface $htmlRenderer
     * @param bool                     $inTightList
     *
     * @return HtmlElement|string
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, bool $inTightList = false)
    {
        if (!($block instanceof AttributesBlockElement)) {
            throw new InvalidArgumentException('Incompatible block type: '.get_class($block));
        }

        return $htmlRenderer->renderInlines($block->children());
    }
}
