<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;

class ParserTest extends TestCase
{
    #[DataProvider('parseProvider')]
    public function testMainFlow(string $pathFile, array $expectedResult): void
    {
        $parser = new Parser();
        $actual = $parser->parse($pathFile);

        $this->assertEquals($expectedResult, $actual);
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

    public static function parseProvider(): array
    {

        $fixturesDir = __DIR__ . "/fixtures/";

        $expectedFile1 = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];
        $expectedFile2 = [
            "timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"
        ];

        return [
            'Parse file1.json' => [
                $fixturesDir . "file1.json",
                $expectedFile1
            ],
            'Parse file2.json' => [
                $fixturesDir . "file2.json",
                $expectedFile2
            ]
        ];
    }
}
