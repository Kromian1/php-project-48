<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

class FunctionsTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/fixtures/';
    #[DataProvider('mainFlowProvider')]
    public function testMainFlow(string $pathFile1, string $pathFile2, string $expectedStylish, string $expectedPlain): void
    {
        $actualStylish = genDiff($pathFile1, $pathFile2, 'stylish');
        $actualPlain = genDiff($pathFile1, $pathFile2, 'plain');
        $actualJson = genDiff($pathFile1, $pathFile2, 'json');
        $actualDefault = genDiff($pathFile1, $pathFile2);
        $decoded = json_decode($actualJson, true);

        $this->assertJson($actualJson);
        $this->assertIsArray($decoded);
        $this->assertEquals($expectedStylish, $actualStylish);
        $this->assertEquals($expectedPlain, $actualPlain);
        $this->assertEquals($actualDefault, $actualStylish);
    }

    #[DataProvider('notExistingFilesProvider')]
    public function testGendiffWithNotExistingFiles(string $file1, string $file2, string $expectedError): void
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches($expectedError);

        genDiff($file1, $file2);
    }

    public static function notExistingFilesProvider(): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $nonExisting = sys_get_temp_dir() . '/non_existing_' . uniqid() . '.json';
        
        return [
            'First file does not exist' => [
                $nonExisting,
                $tempFile,
                '/is not found/'
            ],
            'Second file does not exist' => [
                $tempFile,
                $nonExisting,
                '/is not found/'
            ]
        ];
    }

    public static function mainFlowProvider(): array
    {
        $expectedStylish = <<<EXPECTED
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}

EXPECTED;

        $expectedPlain = <<<PLAIN
    Property 'follow' was removed
    Property 'proxy' was removed
    Property 'timeout' was updated. From 50 to 20
    Property 'verbose' was added with value: true
    PLAIN;

        return [
            'Paths to files JSON' => [
                self::FIXTURESDIR . "file1.json",
                self::FIXTURESDIR . "file2.json",
                $expectedStylish,
                $expectedPlain
            ]
        ];
    }
}
