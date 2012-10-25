<?php
/**
 * Fichier commun du Framework.
 *
 * Ce fichier permet d'inclure ce qu'il faut au minimum pour faire tourner SWAF.
 * Note que le Framework est écrit en considérant que ce fichier est inclus, il 
 * faut donc obligatoirement l'inclure.
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
define('SWAF_VERSION', '0.1-12-10');
define('CORE_DIR',     __DIR__);
define('MAIN_DIR',     realpath(CORE_DIR.'/../'));
define('CACHE_DIR',    MAIN_DIR.DIRECTORY_SEPARATOR.'cache');

require 'exceptions.php';
require 'FileManager.class.php';
require 'Router.class.php';
require 'Controller.class.php';
require 'Module.class.php';
require 'Core.class.php';
require 'Template.class.php';

require 'errorHandler.php';

$CORE = new \SWAF\Core\Core();
$CORE->init();

?>
