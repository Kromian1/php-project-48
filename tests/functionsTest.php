<?php

namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Gendiff\genDiff;

class functionsTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    #[DataProvider('mainFlowProvider')]
    public function testGenDiff(string $pathFile1, string $pathFile2, string $expectedDifferJsons): void
    {
        $actual = genDiff($pathFile1, $pathFile2);

        $this->assertEquals($expectedDifferJsons, $actual);
    }

    public static function mainFlowProvider(): array
    {
        $expectedDifferJsons = <<<EXPECTED
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}

EXPECTED;

        return [
            'Paths to files JSON' => [
                self::FIXTURESDIR . "file1.json",
                self::FIXTURESDIR . "file2.json",
                $expectedDifferJsons
            ]
        ];
    }
}
