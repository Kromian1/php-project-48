<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Formatters\Stylish;

class StylishTest extends TestCase
{
    private Stylish $formatter;

    protected function setUp(): void
    {
        $this->formatter = new Stylish();
    }

    public function testFormatSimpleAdded(): void
    {
        $diff = [['key' => 'test', 'type' => 'added', 'value' => 'value']];
        $result = $this->formatter->format($diff);
        
        $this->assertStringContainsString('+ test: value', $result);
    }

        public function testFormatSimpleRemoved(): void
    {
        $diff = [['key' => 'test', 'type' => 'removed', 'value' => 'value']];
        $result = $this->formatter->format($diff);
        
        $this->assertStringContainsString('- test: value', $result);
    }

        public function testFormatSimpleUnchanged(): void
    {
        $diff = [['key' => 'test', 'type' => 'unchanged', 'value' => 'value']];
        $result = $this->formatter->format($diff);
        
        $this->assertStringContainsString('test: value', $result);
        $this->assertStringNotContainsString('+', $result);
        $this->assertStringNotContainsString('-', $result);
    }
    
    public function testFormatChanged(): void
    {
        $diff = [[
            'key' => 'test',
            'type' => 'changed',
            'oldValue' => 'old',
            'newValue' => 'new'
        ]];
        
        $result = $this->formatter->format($diff);
        
        $this->assertStringContainsString('- test: old', $result);
        $this->assertStringContainsString('+ test: new', $result);
    }

    public function testFormatComplexValue(): void
    {
        $formatter = new Stylish();
    
        $diff = [[
            'key' => 'obj',
            'type' => 'added',
            'value' => json_decode('{"inner": "value"}')
        ]];
    
        $result = $formatter->format($diff);
    
        $this->assertStringContainsString('+ obj:', $result);
        $this->assertStringContainsString('inner: value', $result);
    }

    public function testFormatNested(): void
    {
        $diff = [[
            'key' => 'parent',
            'type' => 'nested',
            'children' => [
                ['key' => 'child', 'type' => 'added', 'value' => 'value']
            ]
        ]];
    
        $result = $this->formatter->format($diff);
    

        $this->assertStringContainsString('parent: {', $result);
        $this->assertStringContainsString('+ child: value', $result);
        $this->assertStringContainsString('}', $result);
    }

    public function testFormatThrowsExceptionForUnknownType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown type: invalid');
    
        $diff = [['type' => 'invalid', 'key' => 'test', 'value' => 'value']];
        $this->formatter->format($diff);
    }

    public function testFormatSpecialValues(): void
    {
        $diff = [
            ['key' => 'null_val', 'type' => 'added', 'value' => null],
            ['key' => 'empty_str', 'type' => 'added', 'value' => ''],
            ['key' => 'bool_true', 'type' => 'added', 'value' => true],
            ['key' => 'bool_false', 'type' => 'added', 'value' => false],
        ];
    
        $result = $this->formatter->format($diff);
    
        $this->assertStringContainsString('+ null_val: null', $result);
        $this->assertStringContainsString('+ empty_str:', $result);
        $this->assertStringContainsString('+ bool_true: true', $result);
        $this->assertStringContainsString('+ bool_false: false', $result);
    }

}