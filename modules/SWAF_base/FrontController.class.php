<?php

namespace SWAF;

class FrontController extends \SWAF\Core\Controller
{
    public function home ()
    {
        self::module()->display('home.html');
    }

    public function test ($page, $machin, $ext)
    {
        echo "<h1>$page</h1><p>";
        echo "Page : $machin<br/>";
        if ($ext != '') {
            echo "ext : $ext";
        }
        echo '</p>';
    }
}

?>
