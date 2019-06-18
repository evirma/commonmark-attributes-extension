<?php

namespace Evirma\CommonMark\EventListener;

use Evirma\CommonMark\AttributesHelper;
use Evirma\CommonMark\Block\Element\AttributesBlockElement;
use Evirma\CommonMark\Inline\Element\AttributesInlineElement;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Node\Node;

class DocumentParsedEventListener
{
    const DIRECTION_PREFIX = 'prefix';

    const DIRECTION_SUFFIX = 'suffix';

    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function onDocumentParsed(DocumentParsedEvent $parsedEvent)
    {
        $document = $parsedEvent->getDocument();
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if (!$event->isEntering() && $node instanceof AttributesBlockElement) {
                list($target, $direction) = $this->findTargetAndDirection($node);

                if ($target) {
                    if (($parent = $target->parent()) instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                        $target = $parent;
                    }

                    if (self::DIRECTION_SUFFIX === $direction) {
                        $attributes = AttributesHelper::merge($target, $node->getAttributes());
                    } else {
                        $attributes = AttributesHelper::merge($node->getAttributes(), $target);
                    }

                    $target->data['attributes'] = $attributes;
                }

                if ($node instanceof AbstractBlock && $node->endsWithBlankLine() && $node->next() && $node->previous()) {
                    $previous = $node->previous();
                    if ($previous instanceof AttributesBlockElement) {
                        $previous->setLastLineBlank(true);
                    }
                }

                $node->detach();
            }
        }

        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node instanceof AttributesInlineElement) {
                if ($node->isBlock()) {
                    /** @var Node $target */
                    $target = $node->parent();

                    if ($target->isCode()) {
                        continue;
                    }

                    if ($target && ($parent = $target->parent()) instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                        $target = $parent;
                    }
                } else {
                    $target = $node->previous();
                }

                $target->data['attributes'] = AttributesHelper::merge($target, $node->getAttributes());
            }
        }
    }

    private function findTargetAndDirection(Node $node)
    {
        $target = null;
        $direction = null;
        $previous = $next = $node;
        while (true) {
            $previous = $this->getPrevious($previous);
            $next = $this->getNext($next);

            if (null === $previous && null === $next) {
                $target = $node->parent();
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if (null !== $previous && !$previous instanceof AttributesBlockElement) {
                $target = $previous;
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if (null !== $next && !$next instanceof AttributesBlockElement) {
                $target = $next;
                $direction = self::DIRECTION_PREFIX;

                break;
            }
        }

        return [$target, $direction];
    }

    private function getPrevious(Node $node = null)
    {
        $previous = $node instanceof Node ? $node->previous() : null;

        if ($previous instanceof AbstractBlock && $previous->endsWithBlankLine()) {
            $previous = null;
        }

        return $previous;
    }

    private function getNext(Node $node = null)
    {
        if ($node instanceof Node) {
            return $node instanceof AbstractBlock && $node->endsWithBlankLine() ? null : $node->next();
        }

        return false;
    }
}
