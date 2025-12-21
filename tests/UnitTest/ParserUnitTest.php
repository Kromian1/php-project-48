<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;
use Gendiff\Differ;

class ParserUnitTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/../fixtures/';
    private const EXPECTEDDIR = __DIR__ . '/../fixtures/expected/';

    public function testEmptyExtension(): void
{
        $parser = new Parser();
        $differ = new Differ();

        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        file_put_contents($tempFile, '{}');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $dataFile = $differ->getFileData($tempFile);
        $parser->parse($dataFile, $tempFile);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function testInvalidExtension(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $invalidFile = $tempFile . '.ext';
        rename($tempFile, $invalidFile);

        $differ = new Differ();
        $parser = new Parser();

        $dataFile = $differ->getFileData($invalidFile);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $parser->parse($dataFile, $invalidFile);

        if (file_exists($invalidFile)) {
            unlink($invalidFile);
        }
    }
}
