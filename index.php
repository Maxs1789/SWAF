<?php
/**
 * Fichier d'index.
 *
 * PHP version 5
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  PHP License 3.01
 * @version  GIT: git://github.com/Maxs1789/SWAF.git
 * @link     https://github.com/Maxs1789/SWAF
 */
require_once 'core/common.php';

$CORE->call();

$tpl = new \SWAF\Core\Template();

$tpl->assignVars(
    array(
        'show_users' => true,
        'users'      => array(
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
    )
);

$tpl->show('styles/test.html');

?>
