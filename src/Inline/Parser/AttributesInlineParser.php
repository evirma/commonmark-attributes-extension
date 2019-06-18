<?php

namespace Evirma\CommonMark\Inline\Parser;

use Evirma\CommonMark\AttributesHelper;
use Evirma\CommonMark\Inline\Element\AttributesInlineElement;
use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

final class AttributesInlineParser implements InlineParserInterface
{
    /**
     * @return string[]
     */
    public function getCharacters(): array
    {
        return [' ', '{'];
    }

    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        if ('{' !== $cursor->getNextNonSpaceCharacter()) {
            return false;
        }

        $char = $cursor->getCharacter();
        if ('{' === $char) {
            $char = (string) $cursor->getCharacter($cursor->getPosition() - 1);
        }

        $attributes = AttributesHelper::parse($cursor);
        if (empty($attributes)) {
            return false;
        }

        if ('' === $char) {
            $cursor->advanceToNextNonSpaceOrNewline();
        }

        $node = new AttributesInlineElement($attributes, ' ' === $char || '' === $char);
        $inlineContext->getContainer()->appendChild($node);

        $delimiter = new Delimiter('attributes', 1, $node, true, true);
        $inlineContext->getDelimiterStack()->push($delimiter);

        return true;
    }
}
