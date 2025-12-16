<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;

class ParserTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile, object $expected): void
    {
        $parser = new Parser();
        $actual = $parser->parse($pathFile);

        $this->assertEquals($expected, $actual);
    }
    public function testEmptyExtension(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $parser = new Parser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $parser->parse($tempFile);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function testInvalidExtension(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $invalidFile = $tempFile . '.ext';
        rename($tempFile, $invalidFile);

        $parser = new Parser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $parser->parse($invalidFile);

        if (file_exists($invalidFile)) {
            unlink($invalidFile);
        }
    }

    public function testFileReadError(): void
    {
        $parser = new Parser();
        $nonExistentFile = '/tmp/nonexistent_' . uniqid() . '.json';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to read file: $nonExistentFile");
        
        $parser->parse($nonExistentFile);
    }

    public static function mainFlowProvider(): array
    {
        $expectedParseFile1 = new \stdClass();
        $expectedParseFile1->host = 'hexlet.io';
        $expectedParseFile1->timeout = 50;
        $expectedParseFile1->proxy = '123.234.53.22';
        $expectedParseFile1->follow = false;

        $expectedParseFile2 = new \stdClass();
        $expectedParseFile2->timeout = 20;
        $expectedParseFile2->verbose = true;
        $expectedParseFile2->host = 'hexlet.io';



        return [
            'Parse file1.json' => [self::FIXTURESDIR . "file1.json", $expectedParseFile1],
            'Parse file2.json' => [self::FIXTURESDIR . "file2.json", $expectedParseFile2],
            'Parse file1.yml' => [self::FIXTURESDIR . "file1.yml", $expectedParseFile1],
            'Parse file2.yaml' => [self::FIXTURESDIR . "file2.yaml", $expectedParseFile2],
        ];
    }
}
