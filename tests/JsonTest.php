<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Json;

class JsonTest extends TestCase
{
    public function testJsonFormat(): void
    {
        $formatter = new Json();

        $diff = [
            [
                'key' => 'follow',
                'type' => 'added',
                'value' => false
            ],
            [
                'key' => 'host',
                'type' => 'unchanged',
                'value' => 'hexlet.io'
            ]
        ];

        $result = $formatter->format($diff);

        $decoded = json_decode($result, true);

        $this->assertJson($result);
        $this->assertEquals($diff, $decoded);
    }

    public function testJsonFormatEmpty(): void
    {
        $formatter = new Json();
        $diff = [];
        $result = $formatter->format($diff);
        $this->assertEquals('[]', $result);
    }
    public function testJsonFormatComplex(): void
    {
        $formatter = new Json();

        $diff = [
            [
                'key' => 'common',
                'type' => 'nested',
                'children' => [
                    [
                        'key' => 'follow',
                        'type' => 'added',
                        'value' => false
                    ]
                ]
            ]
        ];

        $result = $formatter->format($diff);
        $decoded = json_decode($result, true);

        $this->assertJson($result);
        $this->assertEquals($diff, $decoded);
    }
}
