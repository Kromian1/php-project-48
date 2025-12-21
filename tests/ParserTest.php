<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;
use Gendiff\Differ;

class ParserTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    private const EXPECTEDDIR = __DIR__ . '/fixtures/expected/';

    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile, object $expected): void
    {
        $differ = new Differ();
        $dataFile = $differ->getFileData($pathFile);

        $parser = new Parser();
        $actual = $parser->parse($dataFile, $pathFile);

        $this->assertEquals($expected, $actual);
    }

    public static function mainFlowProvider(): array
    {
        $expectedParseFile1 = json_decode(file_get_contents(self::EXPECTEDDIR . "parsed_file1.json"), false);
        $expectedParseFile2 = json_decode(file_get_contents(self::EXPECTEDDIR . "parsed_file2.json"), false);

        return [
            'Parse file1.json' => [self::FIXTURESDIR . "file1.json", $expectedParseFile1],
            'Parse file2.json' => [self::FIXTURESDIR . "file2.json", $expectedParseFile2],
            'Parse file1.yml' => [self::FIXTURESDIR . "file1.yml", $expectedParseFile1],
            'Parse file2.yaml' => [self::FIXTURESDIR . "file2.yaml", $expectedParseFile2],
        ];
    }
}
