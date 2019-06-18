<?php

namespace Evirma\CommonMark\Extension;

use Evirma\CommonMark\Block\Element\AttributesBlockElement;
use Evirma\CommonMark\Block\Parser\AttributesBlockParser;
use Evirma\CommonMark\Block\Renderer\AttributesBlockRenderer;
use Evirma\CommonMark\EventListener\DocumentParsedEventListener;
use Evirma\CommonMark\Inline\Element\AttributesInlineElement;
use Evirma\CommonMark\Inline\Parser\AttributesInlineParser;
use Evirma\CommonMark\Inline\Renderer\AttributesInlineRenderer;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;

final class AttributesExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $listener = new DocumentParsedEventListener($environment);

        $environment
            ->addEventListener(DocumentParsedEvent::class, [$listener, 'onDocumentParsed'])
            ->addInlineParser(new AttributesInlineParser(), 0)
            ->addInlineRenderer(AttributesInlineElement::class, new AttributesInlineRenderer(), 0)
            ->addBlockParser(new AttributesBlockParser(), 0)
            ->addBlockRenderer(AttributesBlockElement::class, new AttributesBlockRenderer(), 0);
    }
}
