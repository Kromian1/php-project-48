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
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expectedStylish, string $expectedPlain): void
    {
        $actualStylish = genDiff($pathFile1, $pathFile2, 'stylish');
        $actualPlain = genDiff($pathFile1, $pathFile2, 'plain');
        $actualJson = genDiff($pathFile1, $pathFile2, 'json');
        $actualDefault = genDiff($pathFile1, $pathFile2);
        $decoded = json_decode($actualJson, true);

        $this->assertJson($actualJson);
        $this->assertIsArray($decoded);
        $this->assertStringEqualsFile($expectedStylish, $actualStylish);
        $this->assertStringEqualsFile($expectedPlain, $actualPlain);
        $this->assertEquals($actualDefault, $actualStylish);
    }

    public static function mainFlowProvider(): array
    {
        $expectedStylish = self::EXPECTEDDIR . "file1_file2_stylish.txt";
        $expectedPlain = self::EXPECTEDDIR . "file1_file2_plain.txt";

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
