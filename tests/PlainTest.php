<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Plain;

class PlainTest extends TestCase
{
    public function testPlainFormatAdded(): void
    {
        $formatter = new Plain();
        $diff = [['key' => 'follow', 'type' => 'added', 'value' => false]];

        $result = $formatter->format($diff);
        $this->assertEquals("Property 'follow' was added with value: false", $result);
    }

    public function testPlainFormatRemoved(): void
    {
        $formatter = new Plain();
        $diff = [['key' => 'setting2', 'type' => 'removed', 'value' => 200]];

        $result = $formatter->format($diff);
        $this->assertEquals("Property 'setting2' was removed", $result);
    }

    public function testPlainFormatChanged(): void
    {
        $formatter = new Plain();
        $diff = [[
            'key' => 'setting3',
            'type' => 'changed',
            'oldValue' => true,
            'newValue' => null
        ]];

        $result = $formatter->format($diff);
        $this->assertEquals("Property 'setting3' was updated. From true to null", $result);
    }

    public function testPlainFormatNested(): void
    {
        $formatter = new Plain();
        $diff = [[
            'key' => 'common',
            'type' => 'nested',
            'children' => [
                ['key' => 'follow', 'type' => 'added', 'value' => false],
                ['key' => 'setting2', 'type' => 'removed', 'value' => 200]
            ]
        ]];

        $result = $formatter->format($diff);
        $expected = "Property 'common.follow' was added with value: false\n" .
                    "Property 'common.setting2' was removed";
        $this->assertEquals($expected, $result);
    }

    public function testPlainFormatEmpty(): void
    {
        $formatter = new Plain();
        $diff = [];

        $result = $formatter->format($diff);
        $this->assertEquals('', $result);
    }

    public function testPlainFormatAllValueTypes(): void
    {
        $formatter = new Plain();

        $diff = [
            [
                'key' => 'string',
                'type' => 'added',
                'value' => 'text'
            ],
            [
                'key' => 'number',
                'type' => 'added',
                'value' => 42
            ],
            [
                'key' => 'bool',
                'type' => 'added',
                'value' => true
            ],
            [
                'key' => 'null',
                'type' => 'added',
                'value' => null
            ],
            [
                'key' => 'complex',
                'type' => 'added',
                'value' => ['nested' => 'value']
            ]
        ];

        $result = $formatter->format($diff);
        $expected = "Property 'string' was added with value: 'text'\n" .
                    "Property 'number' was added with value: 42\n" .
                    "Property 'bool' was added with value: true\n" .
                    "Property 'null' was added with value: null\n" .
                    "Property 'complex' was added with value: [complex value]";
        $this->assertEquals($expected, $result);
    }
}
