<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Differ;
use Gendiff\Parser;

class DifferTest extends TestCase
{
    private const string FIXTURESDIR = __DIR__ . '/fixtures/';
    private const string EXPECTEDDIR = __DIR__ . '/fixtures/expected/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expected): void
    {
        $differ = new Differ();
        $actual = $differ->genDiff($pathFile1, $pathFile2);

        $this->assertStringEqualsFile($expected, $actual);
    }

    public static function mainFlowProvider(): array
    {
        $pathFile1Json = self::FIXTURESDIR . "file1.json";
        $pathFile2Json = self::FIXTURESDIR . "file2.json";
        $pathFile1Yml = self::FIXTURESDIR . "file1.yml";
        $pathFile2Yaml = self::FIXTURESDIR . "file2.yaml";

        $expectedDifferent = self::EXPECTEDDIR . "file1_file2_stylish.txt";
        $expectedSame = self::EXPECTEDDIR . "file2_file2_stylish.txt";

        return [
            'Paths to different files JSON' => [$pathFile1Json, $pathFile2Json, $expectedDifferent],
            'Paths to different files YML' => [$pathFile1Yml, $pathFile2Yaml, $expectedDifferent],
            'Paths to different files JSON and YML' => [$pathFile1Json, $pathFile2Yaml, $expectedDifferent],
            'Paths to same files JSON' => [$pathFile2Json, $pathFile2Json, $expectedSame],
            'Paths to same files YML' => [$pathFile2Yaml, $pathFile2Yaml, $expectedSame],
            'Paths to same files JSON and YML' => [$pathFile2Json, $pathFile2Yaml, $expectedSame]
        ];
    }
}
