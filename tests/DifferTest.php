<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Differ;

class ParserTest extends TestCase
{

    public function testMainFlow(): void
    {
        $as;
    }

    public static function differProvider(): array
    {

        $fixturesDir = __DIR__ . "/fixtures/";

        //return [
         //   'Parse file1.json' => [$fixturesDir . "file1.json"],
         //   'Parse file2.json' => [$fixturesDir . "file2.json"]
        //];
    }
}
