<?php
namespace guava;

class DicTest extends \PHPUnit_Framework_TestCase {

    protected $di;

    public function setUp()
    {
        $this->di = new Dic();
    }

    public function testLoad()
    {
        $this->assertEquals(
            "test!!!",
            $this->di->load('Sandwich')
        );
    }
}
