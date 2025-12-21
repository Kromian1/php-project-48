<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

class FunctionsTest extends TestCase
{
    private const string FIXTURESDIR = __DIR__ . '/fixtures/';
    private const string EXPECTEDDIR = __DIR__ . '/fixtures/expected/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expectedDefault): void
    {
        $actualDefault = genDiff($pathFile1, $pathFile2);
        $this->assertStringEqualsFile($expectedDefault, $actualDefault);
    }

    public static function mainFlowProvider(): array
{
        $pathFile1Json = self::FIXTURESDIR . "file1.json";
        $pathFile2Json = self::FIXTURESDIR . "file2.json";
        $pathFile1Yml = self::FIXTURESDIR . "file1.yml";
        $pathFile2Yaml = self::FIXTURESDIR . "file2.yaml";

        $expectedDefault = self::EXPECTEDDIR . "file1_file2_stylish.txt";

        return [
            'JSON files' => [
                $pathFile1Json,
                $pathFile2Json,
                $expectedDefault
                
            ],
            'YAML files' => [
                $pathFile1Yml,
                $pathFile2Yaml,
                $expectedDefault
            ]
        ];
    }
}
