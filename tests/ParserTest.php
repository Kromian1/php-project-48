<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;

class ParserTest extends TestCase
{

    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile): void
    {
        $parser = new Parser();
        $actual = $parser->parse($pathFile);
        $expectedResult = json_decode(file_get_contents($pathFile));

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

    public static function mainFlowProvider(): array
    {
        return [
            'Parse file1.json' => [self::FIXTURESDIR . "file1.json"],
            'Parse file2.json' => [self::FIXTURESDIR . "file2.json"]
        ];
    }
}
