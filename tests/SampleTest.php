<?php
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /** @test */
    public function it_should_pass()
    {
        $this->assertEquals(true, true);
    }

    /** @test */
    public function it_should_pass_hello_world()
    {

        $var = "Hello World";
        $this->assertEquals($var, 'Hello World');
    }
}
