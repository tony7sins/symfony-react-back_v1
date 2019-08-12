<?php

namespace App;

use PHPUnit\Framework\TestCase;

/**
 * ClassNameTest
 * @group group
 */
class SimplestTest extends TestCase
{
    /** @test */
    public function testAddition()
    {

        $value = true;

        $array = [
            'key' => 'value'
        ];

        $this->assertEquals(5, 2 + 3, 'Five was expected to be equal to two plus three');
        $this->assertTrue($value);
        $this->assertArrayHasKey('key', $array);
        $this->assertEquals('value', $array['key']);
        $this->assertCount(1, $array);
    }
}
