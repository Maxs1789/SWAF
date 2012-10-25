<?php

class FrontController extends \SWAF\Core\Controller
{
    public function home ()
    {
        echo "<h1>TEST</h1>";
    }

    public function test ($test)
    {
        echo "<h1>TEST</h1>$test";
    }
}

?>
