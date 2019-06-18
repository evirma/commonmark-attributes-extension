League CommonMark Attributes Extension
======================================

The Attributes extension adds a syntax to define HTML-attributes on the various block and inline elements in League CommonMark PHP implementation.
Martin Hasoň wrote extension for commonmark v0.18 and lower, it was fully rebuild for v1.0. Be aware that priority of applying css classes was changed.

Installation
------------

This project can be installed via Composer:

    composer require evirma/commonmark-attributes-extension
    
Usage
-----

```php
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Evirma\CommonMark\Extension\AttributesExtension;

$environment = Environment::createCommonMarkEnvironment();
$environment->addExtension(new AttributesExtension());

$converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

echo $converter->convertToHtml('# Hello World! {#hello}');
```

Syntax
------

The basic syntax was inspired by [Kramdown](http://kramdown.gettalong.org/syntax.html#attribute-list-definitions)‘s Attribute Lists feature.

You can assign any attribute to a block-level element. Just directly prepend or follow the block with a block inline attribute list.
That consists of a left curly brace, optionally followed by a colon, the attribute definitions and a right curly brace:

```markdown
> A nice blockquote
{: title="Blockquote title"}

{#id .class}
## Header
```

As with a block-level element you can assign any attribute to a span-level elements using a span inline attribute list,
that has the same syntax and must immediately follow the span-level element:

```markdown
This is *red*{style="color: red"}.
This is *with id*{#some-id}.

```
