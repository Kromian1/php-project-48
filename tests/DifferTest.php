<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Differ;
use Gendiff\Parser;

use function Funct\Collection\sortBy;

class DifferTest extends TestCase
{

    private const FIXTURESDIR = __DIR__ . '/fixtures/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(object $dataFile1, object $dataFile2, string $expected): void
    {
        $differ = new Differ();
        $actual = $differ->compare($dataFile1, $dataFile2);

        $this->assertEquals($expected, $actual);
    }

    public static function mainFlowProvider(): array
    {
        $parser = new Parser();
        $dataFile1Json = $parser->parse(self::FIXTURESDIR . "file1.json");
        $dataFile2Json = $parser->parse(self::FIXTURESDIR . "file2.json");
        $dataFile1Yml = $parser->parse(self::FIXTURESDIR . "file1.yml");
        $dataFile2Yaml = $parser->parse(self::FIXTURESDIR . "file2.yaml");

        $expectedDifferent = <<<EXPECTED
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}

EXPECTED;

        $expectedSame = <<<EXPECTED
{
    host: hexlet.io
    timeout: 20
    verbose: true
}

EXPECTED;

        return [
            'Parsed and compared different files JSON' => [$dataFile1Json, $dataFile2Json, $expectedDifferent],
            'Parsed and compared different files YML' => [$dataFile1Yml, $dataFile2Yaml, $expectedDifferent],
            'Parsed and compared different files JSON and YML' => [$dataFile1Json, $dataFile2Yaml, $expectedDifferent],
            'Parsed and compared same files JSON' => [$dataFile2Json, $dataFile2Json, $expectedSame],
            'Parsed and compared same files YML' => [$dataFile2Yaml, $dataFile2Yaml, $expectedSame],
            'Parsed and compared same files JSON and YML' => [$dataFile2Json, $dataFile2Yaml, $expectedSame]
        ];
    }

    #[DataProvider('buildDiffProvider')]
    public function testBuildDiff(object $dataFile1, object $dataFile2, array $expected): void
    {
        $differ = new Differ();
        $actual = $differ->buildDiff($dataFile1, $dataFile2);
        $this->assertEquals($expected, $actual);
    }

    public static function buildDiffProvider(): array
    {
        return [
            'unchanged' => [
                (object) ['a' => 1],
                (object) ['a' => 1],
                [['key' => 'a', 'type' => 'unchanged', 'value' => 1]]
            ],
            'added' => [
                (object) [],
                (object) ['a' => 1],
                [['key' => 'a', 'type' => 'added', 'value' => 1]]
            ],
            'removed' => [
                (object) ['a' => 1],
                (object) [],
                [['key' => 'a', 'type' => 'removed', 'value' => 1]]
            ],
            'changed' => [
                (object) ['a' => 1],
                (object) ['a' => 2],
                [['key' => 'a', 'type' => 'changed', 'oldValue' => 1, 'newValue' => 2]]
            ]
        ];
    }

    public function testCompareEmptyFileVsNonEmptyFile(): void
    {
        $emptyJson = $this->createEmptyFile('json');

        $parser = new Parser();
        $parsedNonEmptyJson = $parser->parse(self::FIXTURESDIR . "file1.json");
        $parsedEmptyJson = $parser->parse($emptyJson);

        $differ = new Differ();
        $actual = $differ->compare($parsedEmptyJson, $parsedNonEmptyJson);

        $expected = <<<EXPECTED
{
  + follow: false
  + host: hexlet.io
  + proxy: 123.234.53.22
  + timeout: 50
}

EXPECTED;
        $this->assertEquals($expected, $actual);
    }

    public function testCompareNonEmptyFileVsEmptyFile(): void
    {
        $emptyJson = $this->createEmptyFile('json');

        $parser = new Parser();
        $parsedNonEmptyJson = $parser->parse(self::FIXTURESDIR . "file1.json");
        $parsedEmptyJson = $parser->parse($emptyJson);

        $differ = new Differ();
        $actual = $differ->compare($parsedNonEmptyJson, $parsedEmptyJson);
        $expected = <<<EXPECTED
{
  - follow: false
  - host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
}

EXPECTED;
        $this->assertEquals($expected, $actual);
    }

    public function testCompareEmptyFiles(): void
    {
        $emptyJson1 = $this->createEmptyFile('json');
        $emptyJson2 = $this->createEmptyFile('json');

        $parser = new Parser();
        $parsedEmptyJson1 = $parser->parse($emptyJson1);
        $parsedEmptyJson2 = $parser->parse($emptyJson2);

        $differ = new Differ();

        $actual = $differ->compare($parsedEmptyJson1, $parsedEmptyJson2);
        $expected = "{\n\n}\n";

        $this->assertEquals($expected, $actual);

        unlink($emptyJson1);
        unlink($emptyJson2);
    }
    private function createEmptyFile(string $extension): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');

        match ($extension) {
            'json' => $emptyFile = $tempFile . '.json',
            'yaml', 'yml' => $emptyFile = $tempFile . "{$extension}",
            default => throw new \InvalidArgumentException("Unknown extension\n")
        };

        rename($tempFile, $emptyFile);
        file_put_contents($emptyFile, '{}');
        return $emptyFile;
    }
}
