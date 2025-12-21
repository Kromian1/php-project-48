<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Differ;

class DifferTest extends TestCase
{
    private const string FIXTURESDIR = __DIR__ . '/fixtures/';
    private const string EXPECTEDDIR = __DIR__ . '/fixtures/expected/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, array $expectedFiles): void
    {
        $differ = new Differ();
        $actualStylish = $differ->genDiff($pathFile1, $pathFile2, 'stylish');
        $actualPlain = $differ->genDiff($pathFile1, $pathFile2, 'plain');
        $actualJson = $differ->genDiff($pathFile1, $pathFile2, 'json');
        $actualDefault = $differ->genDiff($pathFile1, $pathFile2);
        $decoded = json_decode($actualJson, true);

        $this->assertJson($actualJson);
        $this->assertIsArray($decoded);
        $this->assertStringEqualsFile($expectedFiles['stylish'], $actualStylish);
        $this->assertStringEqualsFile($expectedFiles['plain'], $actualPlain);
        $this->assertStringEqualsFile($expectedFiles['default'], $actualDefault);
        $this->assertStringEqualsFile($expectedFiles['json'], $actualJson);
    }

    public static function mainFlowProvider(): array
    {
        $pathFile1Json = self::FIXTURESDIR . "file1.json";
        $pathFile2Json = self::FIXTURESDIR . "file2.json";
        $pathFile1Yml = self::FIXTURESDIR . "file1.yml";
        $pathFile2Yaml = self::FIXTURESDIR . "file2.yaml";

        $expectedDefault = self::EXPECTEDDIR . "file1_file2_stylish.txt";
        $expectedStylish = self::EXPECTEDDIR . "file1_file2_stylish.txt";
        $expectedPlain = self::EXPECTEDDIR . "file1_file2_plain.txt";
        $expectedJson = self::EXPECTEDDIR . "file1_file2_json.txt";

        return [
            'JSON files' => [
                $pathFile1Json,
                $pathFile2Json,
                [
                    'default' => $expectedDefault,
                    'stylish' => $expectedStylish,
                    'plain' => $expectedPlain,
                    'json' => $expectedJson
                ]
            ],
            'YAML files' => [
                $pathFile1Yml,
                $pathFile2Yaml,
                [
                    'default' => $expectedDefault,
                    'stylish' => $expectedStylish,
                    'plain' => $expectedPlain,
                    'json' => $expectedJson
                ]
            ]
        ];
    }
}
