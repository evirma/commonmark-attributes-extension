<?php

namespace Evirma\CommonMark\Tests;

use Evirma\CommonMark\Extension\AttributesExtension;
use PHPUnit_Framework_TestCase;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class LocalDataTest extends PHPUnit_Framework_TestCase
{
    /** @var Converter */
    protected $converter;

    protected function setUp()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AttributesExtension());

        $this->converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));
    }

    /**
     * @dataProvider dataProvider
     * @param $markdown
     * @param $html
     * @param $testName
     */
    public function testExample($markdown, $html, $testName)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n".$markdown;
        $failureMessage .= "\n=== expected ===============\n".$html;
        $failureMessage .= "\n=== got ====================\n".$actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $ret = [];
        foreach (glob(__DIR__.'/data/*.md') as $markdownFile) {
            $testName = basename($markdownFile, '.md');
            $markdown = file_get_contents($markdownFile);
            $html = file_get_contents(__DIR__.'/data/'.$testName.'.html');

            $ret[] = [$markdown, $html, $testName];
        }

        return $ret;
    }
}
