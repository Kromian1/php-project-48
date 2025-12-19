<?php

namespace Gendiff\Tests\UnitTest;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Stylish;

class StylishTest extends TestCase
{
    private Stylish $formatter;
    private const string DIR = __DIR__ . '/../fixtures/stylish/';

    protected function setUp(): void
    {
        $this->formatter = new Stylish();
    }

    #[DataProvider('stylishFormatProvider')]
    public function testStylishFormat(string $diffFile, string $expectedFile): void
    {
        $diff = json_decode(file_get_contents($diffFile), true);
        $expected = file_get_contents($expectedFile);

        $result = $this->formatter->format($diff);
        $this->assertEquals($expected, $result);
    }

    public static function stylishFormatProvider(): array
    {
        return [
            'simple_added' => [self::DIR . 'simple_added_diff.json', self::DIR . 'simple_added_expected.txt'],
            'simple_removed' => [self::DIR . 'simple_removed_diff.json', self::DIR . 'simple_removed_expected.txt'],
            'simple_unchanged' => [self::DIR . 'simple_unchanged_diff.json', self::DIR . 'simple_unchanged_expected.txt'],
            'changed' => [self::DIR . 'changed_diff.json', self::DIR . 'changed_expected.txt'],
            'complex_value' => [self::DIR . 'complex_value_diff.json', self::DIR . 'complex_value_expected.txt'],
            'nested' => [self::DIR . 'nested_diff.json', self::DIR . 'nested_expected.txt'],
            'special_values' => [self::DIR . 'special_values_diff.json', self::DIR . 'special_values_expected.txt'],
        ];
    }

    public function testFormatThrowsExceptionForUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown type: invalid');

        $diff = [['type' => 'invalid', 'key' => 'test', 'value' => 'value']];
        $this->formatter->format($diff);
    }
}
