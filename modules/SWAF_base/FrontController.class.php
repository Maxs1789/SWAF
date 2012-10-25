<?php

namespace SWAF;

class FrontController extends \SWAF\Core\Controller
{
    public function home ()
    {
        echo "<h1>Home</h1>";
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
