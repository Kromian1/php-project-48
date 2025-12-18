<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

class FunctionsTest extends TestCase
{
    private const string FIXTURESDIR = __DIR__ . '/fixtures/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expectedStylish, string $expectedPlain): void
    {
        $actualStylish = genDiff($pathFile1, $pathFile2, 'stylish');
        $actualPlain = genDiff($pathFile1, $pathFile2, 'plain');
        $actualJson = genDiff($pathFile1, $pathFile2, 'json');
        $actualDefault = genDiff($pathFile1, $pathFile2);
        $decoded = json_decode($actualJson, true);

        $this->assertJson($actualJson);
        $this->assertIsArray($decoded);
        $this->assertEquals($expectedStylish, $actualStylish);
        $this->assertEquals($expectedPlain, $actualPlain);
        $this->assertEquals($actualDefault, $actualStylish);
    }

    public static function mainFlowProvider(): array
    {
        $expectedStylish = <<<EXPECTED
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}


EXPECTED;

        $expectedPlain = <<<PLAIN
    Property 'follow' was removed
    Property 'proxy' was removed
    Property 'timeout' was updated. From 50 to 20
    Property 'verbose' was added with value: true
    
    PLAIN;

        return [
            'Paths to files JSON' => [
                self::FIXTURESDIR . "file1.json",
                self::FIXTURESDIR . "file2.json",
                $expectedStylish,
                $expectedPlain
            ]
        ];
    }
}
