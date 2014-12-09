<?php
namespace guava\Controllers;

class Smoothie
{
    protected $ingredients;

    /**
     * @param Array $ingredients
     */
    public function __construct($ingredients)
    {
        $this->ingredients = $ingredients;
    }
    public function blend(){
        return shuffle($this->ingredients);
    }
}