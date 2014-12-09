<?php
namespace guava;

use guava\Dependencies\Bun;
use guava\Dependencies\Cheese;
use guava\Dependencies\Meat;

class DicTest extends \PHPUnit_Framework_TestCase {

    protected $di;

    public function setUp()
    {
        $this->di = new Dic();
    }

    public function testLoad()
    {
        $this->assertEquals(
            array(new Bun(), new Cheese(), new Meat()),
            $this->di->load('Sandwich')
        );
    }

    public function testGetDependencies()
    {
        $this->di->load('Sandwich');
        $this->assertEquals(
            array(new Bun(), new Cheese(), new Meat()),
            $this->di->getDependencies('Sandwich')
        );
    }

    public function testIsLoaded()
    {
        // tests before Sandwich is loaded
        $this->assertEquals(
            false,
            $this->di->isLoaded('Sandwich')
        );

        // tests after Sandwich is loaded
        $this->di->load('Sandwich');
        $this->assertEquals(
            true,
            $this->di->isLoaded('Sandwich')
        );
        $this->assertEquals(
            true,
            $this->di->isLoaded('Sandwich', true)
        );

        // tests after cache is cleared
        $this->di->clearCache('Sandwich');
        $this->assertEquals(
            false,
            $this->di->isLoaded('Sandwich', true)
        );
        $this->assertEquals(
            false,
            $this->di->isLoaded('Sandwich')
        );

    }

    public function testClearCache()
    {
        $this->di->load('Sandwich');
        $this->assertEquals(
            true,
            $this->di->clearCache('Sandwich')
        );
        $this->assertEquals(
            true,
            $this->di->clearCache()
        );
    }
}
