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
        $actual = $differ->compare($dataFile1, $dataFile2)->__toString();

        $this->assertEquals($expected, $actual);
    }

    public static function mainFlowProvider(): array
    {
        $parser = new Parser();
        $dataFile1Json = $parser->parse(self::FIXTURESDIR . "file1.json");
        $dataFile2Json = $parser->parse(self::FIXTURESDIR . "file2.json");
        $dataFile3Yml = $parser->parse(self::FIXTURESDIR . "file3.yml");
        $dataFile4Yaml = $parser->parse(self::FIXTURESDIR . "file4.yaml");

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
            'Parsed and compared different files YML' => [$dataFile3Yml, $dataFile4Yaml, $expectedDifferent],
            'Parsed and compared different files JSON and YML' => [$dataFile1Json, $dataFile4Yaml, $expectedDifferent],
            'Parsed and compared same files JSON' => [$dataFile2Json, $dataFile2Json, $expectedSame],
            'Parsed and compared same files YML' => [$dataFile4Yaml, $dataFile4Yaml, $expectedSame],
            'Parsed and compared same files JSON and YML' => [$dataFile2Json, $dataFile4Yaml, $expectedSame]
        ];
    }

    public function testCompareEmptyFileVsNonEmptyFile(): void
    {
        $emptyJson = $this->createEmptyFile('json');

        $parser = new Parser();
        $parsedNonEmptyJson = $parser->parse(self::FIXTURESDIR . "file1.json");
        $parsedEmptyJson = $parser->parse($emptyJson);

        $differ = new Differ();
        $actual = $differ->compare($parsedEmptyJson, $parsedNonEmptyJson)->__toString();

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
        $actual = $differ->compare($parsedNonEmptyJson, $parsedEmptyJson)->__toString();
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

        $actual = $differ->compare($parsedEmptyJson1, $parsedEmptyJson2)->__toString();
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
