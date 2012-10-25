<?php

class FrontController extends \SWAF\Core\Controller
{
    public function home ()
    {
        echo "<h1>Test</h1>";
    }

    public function test ($test)
    {
        echo "<h1>Test</h1>$test";
    }
}

?>
