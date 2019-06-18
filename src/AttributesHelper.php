<?php

namespace Evirma\CommonMark;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Cursor;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Util\RegexHelper;

class AttributesHelper
{
    const REGEXP_ATTR_PARSER = '/^\s*([.#][_a-z0-9-]+|'.RegexHelper::PARTIAL_ATTRIBUTENAME.RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC.')/i';

    public static function parse(Cursor $cursor)
    {
        $state = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrNewline();
        if ('{' !== $cursor->getCharacter()) {
            $cursor->restoreState($state);

            return [];
        }

        $cursor->advanceBy(1);
        if (':' === $cursor->getCharacter()) {
            $cursor->advanceBy(1);
        }

        $attributes = [];
        while ($attribute = trim($cursor->match(self::REGEXP_ATTR_PARSER))) {
            if ('#' === $attribute[0]) {
                $attributes['id'] = mb_substr($attribute, 1);
                continue;
            }

            if ('.' === $attribute[0]) {
                $attributes['class'][] = mb_substr($attribute, 1);
                continue;
            }

            list($name, $value) = explode('=', $attribute, 2);
            $name = mb_strtolower(trim($name));
            $value = preg_replace('/(^[\"\']|[\"\']$)/', '', $value);

            if ('class' === $name) {
                $value = trim(preg_replace('#\s*#', ' ', $value));
                foreach (explode(' ', $value) as $class) {
                    $attributes['class'][] = $class;
                }
            } else {
                $attributes[trim($name)] = trim($value);
            }
        }

        if (null === $cursor->match('/}/')) {
            $cursor->restoreState($state);

            return [];
        }

        if (!count($attributes)) {
            $cursor->restoreState($state);

            return [];
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = implode(' ', $attributes['class']);
        }

        return $attributes;
    }

    public static function merge($attributes1, $attributes2)
    {
        $attributes = [];
        foreach ([$attributes1, $attributes2] as $arg) {
            if ($arg instanceof AbstractBlock || $arg instanceof AbstractInline) {
                $arg = isset($arg->data['attributes']) ? $arg->data['attributes'] : [];
            }

            $arg = (array) $arg;
            if (isset($arg['class'])) {
                foreach (array_filter(explode(' ', trim($arg['class']))) as $class) {
                    $attributes['class'][] = $class;
                }
                unset($arg['class']);
            }
            $attributes = array_merge($attributes, $arg);
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = implode(' ', $attributes['class']);
        }

        return $attributes;
    }
}
