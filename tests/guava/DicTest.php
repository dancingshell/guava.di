<?php
namespace guava;

use guava\Dependencies\Bun;
use guava\Dependencies\Cheese;
use guava\Dependencies\Turkey;

class DicTest extends \PHPUnit_Framework_TestCase {

    protected $di;

    public function setUp()
    {
        $this->di = new Dic();
        $this->di->lazySet('Sandwich', 'Turkey', 'guava\\Dependencies\\Turkey');
    }

    public function testLoad()
    {
        $this->assertEquals(
            array(new Bun(), new Cheese(), new Turkey()),
            $this->di->load('Sandwich', 'Turkey')
        );
    }

    public function testGetDependencies()
    {
        $this->di->load('Sandwich', 'Turkey');
        $this->assertEquals(
            array(new Bun(), new Cheese(), new Turkey()),
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
        $this->di->load('Sandwich', 'Turkey');
        $this->assertEquals(
            true,
            $this->di->isLoaded('Sandwich')
        );
        $this->assertEquals(
            true,
            $this->di->isLoaded('Sandwich', true)
        );
        // tests class loaded with no constructor dependencies
        $this->di->load('Smoothie');
        $this->assertEquals(
            true,
            $this->di->isLoaded('Smoothie')
        );
        $this->assertEquals(
            false,
            $this->di->isLoaded('Smoothie', true)
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
        $this->di->load('Sandwich', 'Turkey');
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
