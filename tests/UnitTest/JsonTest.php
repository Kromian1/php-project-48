<?php

namespace Gendiff\Tests\UnitTest;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Json;

class JsonTest extends TestCase
{
    private const string DIR = __DIR__ . '/../fixtures/json/';

    #[DataProvider('jsonFormatProvider')]
    public function testJsonFormat(string $diffFile): void
    {
        $formatter = new Json();
        
        $diff = json_decode(file_get_contents($diffFile), true);
        $result = $formatter->format($diff);
        
        $decoded = json_decode($result, true);
        
        $this->assertJson($result);
        $this->assertEquals($diff, $decoded);
    }

    public static function jsonFormatProvider(): array
    {
        return [
            'simple' => [self::DIR . 'simple_diff.json'],
            'complex' => [self::DIR . 'complex_diff.json'],
            'empty' => [self::DIR . 'empty_diff.json']
        ];
    }
}
