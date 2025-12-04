<?php

namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Gendiff\genDiff;

class functionsTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expectedDifferJsons): void
    {
        $actual = genDiff($pathFile1, $pathFile2);

        $this->assertEquals($expectedDifferJsons, $actual);
    }

    public function testGendiffWithNotExistingFile(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $nonExistingFile = sys_get_temp_dir() . '/non_existing_' . uniqid() . '.json';

        $this->expectException(\RuntimeException::class);
        gendiff($nonExistingFile, $tempFile);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
    public static function mainFlowProvider(): array
    {
        $expectedDifferJsons = <<<EXPECTED
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
            'Paths to files JSON' => [
                self::FIXTURESDIR . "file1.json",
                self::FIXTURESDIR . "file2.json",
                $expectedDifferJsons
            ]
        ];
    }
}
