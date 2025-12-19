<?php

namespace Gendiff\Tests\UnitTest;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Plain;

class PlainTest extends TestCase
{
    private const string DIR = __DIR__ . '/../fixtures/plain/';

    #[DataProvider('plainFormatProvider')]
    public function testPlainFormat(string $diffFile, string $expectedFile): void
    {
        $formatter = new Plain();

        $diff = json_decode(file_get_contents($diffFile), true);
        $expected = file_get_contents($expectedFile);

        $result = $formatter->format($diff);
        $this->assertEquals($expected, $result);
    }

    public static function plainFormatProvider(): array
    {
        return [
            'added' => [self::DIR . 'added_diff.json', self::DIR . 'added_expected.txt'],
            'removed' => [self::DIR . 'removed_diff.json', self::DIR . 'removed_expected.txt'],
            'changed' => [self::DIR . 'changed_diff.json', self::DIR . 'changed_expected.txt'],
            'nested' => [self::DIR . 'nested_diff.json', self::DIR . 'nested_expected.txt'],
            'empty' => [self::DIR . 'empty_diff.json', self::DIR . 'empty_expected.txt'],
            'all_types' => [self::DIR . 'all_types_diff.json', self::DIR . 'all_types_expected.txt'],
        ];
    }
}
