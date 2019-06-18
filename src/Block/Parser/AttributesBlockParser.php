<?php

namespace Evirma\CommonMark\Block\Parser;

use Evirma\CommonMark\AttributesHelper;
use Evirma\CommonMark\Block\Element\AttributesBlockElement;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

class AttributesBlockParser implements BlockParserInterface
{
    /**
     * @param ContextInterface $context
     * @param Cursor           $cursor
     *
     * @return bool
     */
    public function parse(ContextInterface $context, Cursor $cursor): bool
    {
        $state = $cursor->saveState();
        $attributes = AttributesHelper::parse($cursor);
        if (empty($attributes)) {
            return false;
        }

        if (null !== $cursor->getNextNonSpaceCharacter()) {
            $cursor->restoreState($state);

            return false;
        }

        $context->addBlock(new AttributesBlockElement($attributes));
        $context->setBlocksParsed(true);

        return true;
    }
}
