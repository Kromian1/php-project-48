<?php

namespace Gendiff\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Differ;

class DifferUnitTest extends TestCase
{
    private const string FIXTURESDIR = __DIR__ . '/../fixtures/';
    private const string EXPECTEDDIR = __DIR__ . '/../fixtures/expected/';

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
        $nonEmptyJson = self::FIXTURESDIR . "file1.json";

        $differ = new Differ();
        $actual = $differ->genDiff($emptyJson, $nonEmptyJson);

        $expected = self::EXPECTEDDIR . "empty_file1_stylish.txt";
        $this->assertStringEqualsFile($expected, $actual);
    }

    public function testCompareNonEmptyFileVsEmptyFile(): void
    {
        $emptyJson = $this->createEmptyFile('json');
        $pathFile1Json = self::FIXTURESDIR . "file1.json";

        $differ = new Differ();
        $actual = $differ->genDiff($pathFile1Json, $emptyJson);
        $expected = self::EXPECTEDDIR . "file1_empty_stylish.txt";
        $this->assertStringEqualsFile($expected, $actual);
    }

    public function testBuildDiffSortKeys(): void
    {
        $differ = new differ();

        $object1 = (object) ['z' => 1, 'a' => 2, 'm' => 3];
        $object2 = (object) ['z' => 1, 'a' => 2, 'm' => 3];

        $diff = $differ->buildDiff($object1, $object2);

        $keys = array_column($diff, 'key');
        $this->assertEquals(['a', 'm', 'z'], $keys);
    }

    public function testBuildDiffNested(): void
    {
        $differ = new Differ();

        $obj1 = json_decode('{"nested": {"inner": "old"}}');
        $obj2 = json_decode('{"nested": {"inner": "new"}}');

        $diff = $differ->buildDiff($obj1, $obj2);
        $expected = json_decode(file_get_contents(self::EXPECTEDDIR . "nested_diff.json"), true);

        $this->assertEquals($expected, $diff);
    }

    public function testCompareWithJsonFormat(): void
    {
        $differ = new Differ();

        $pathFile1Json = self::FIXTURESDIR . "file1.json";
        $pathFile2Json = self::FIXTURESDIR . "file2.json";

        $result = $differ->genDiff($pathFile1Json, $pathFile2Json, 'json');

        $this->assertJson($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey(0, $decoded);
        $this->assertEquals('follow', $decoded[0]['key']);
    }

    public function testCompareEmptyFiles(): void
    {
        $emptyJson1 = $this->createEmptyFile('json');
        $emptyJson2 = $this->createEmptyFile('json');

        $differ = new Differ();

        $actual = $differ->genDiff($emptyJson1, $emptyJson2);
        $expected = "{\n\n}\n\n";

        $this->assertEquals($expected, $actual);

        unlink($emptyJson1);
        unlink($emptyJson2);
    }

    public function testFile1ReadError(): void
    {
        $nonExistentFile1 = '/tmp/nonexistent_' . uniqid() . '.json';
        $pathFile2Json = self::FIXTURESDIR . "file2.json";

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to read file: $nonExistentFile1");

        $differ = new Differ();
        $differ->genDiff($nonExistentFile1, $pathFile2Json);
    }

    public function testFile2ReadError(): void
    {
        $nonExistentFile2 = '/tmp/nonexistent_' . uniqid() . '.json';
        $pathFile1Json = self::FIXTURESDIR . "file2.json";

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Unable to read file: $nonExistentFile2");

        $differ = new Differ();
        $differ->genDiff($pathFile1Json, $nonExistentFile2);
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
