<?php

class FrontController extends \SWAF\Core\Controller
{
    public function home ()
    {
        echo "<h1>Test:home</h1>";

        self::setVar(
            'users',
            array(
                array(
                    'id'    => 0,
                    'log'   => 'maxs',
                    'rdate' => '2012-10-11'
                ),
                array(
                    'id'    => 1,
                    'log'   => 'user0',
                    'rdate' => '2012-10-15'
                ),
                array(
                    'id'    => 2,
                    'log'   => 'john',
                    'rdate' => '2013-01-21'
                )
            )
        );
        self::setVar('show_users', true);

        self::module()->display('test.html');
    }

    public function test ($test)
    {
        echo "<h1>Test</h1>$test";
    }
}

?>
