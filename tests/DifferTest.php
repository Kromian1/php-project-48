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
        $dataFile1 = $parser->parse(self::FIXTURESDIR . "file1.json");
        $dataFile2 = $parser->parse(self::FIXTURESDIR . "file2.json");

        $expected = <<<EXPECTED
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
            'Parsed and compared files' => [$dataFile1, $dataFile2, $expected]
        ];
    }

    public function testCompareEmptyJsonVsNonEmptyJson(): void
    {
        $emptyJson = $this->createEmptyJsonFile();

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

    public function testCompareNonEmptyJsonVsEmptyJson(): void
    {
        $emptyJson = $this->createEmptyJsonFile();

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

    public function testCompareEmptyJsons(): void
    {
        $emptyJson1 = $this->createEmptyJsonFile();
        $emptyJson2 = $this->createEmptyJsonFile();

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
    private function createEmptyJsonFile(): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $emptyJson = $tempFile . '.json';
        rename($tempFile, $emptyJson);
        file_put_contents($emptyJson, '{}');
        return $emptyJson;
    }
}
