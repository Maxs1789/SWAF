<?php
/**
 * Fichier de gestion des erreurs.
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
namespace SWAF\Core;

/**
 * Gestion d'exception.
 *
 * @param Exception $ex Exception.
 *
 * @return null
 */
function exceptionHandler ($ex)
{   
    $code  = $ex->getCode();
    $msg   = $ex->getMessage();
    $file  = $ex->getFile();
    $line  = $ex->getLine();
    $class = get_class($ex);

    echo "<b>$class </b>: $msg<br/>\n";
    echo " $file:$line<br/>\n";
}

set_exception_handler("\SWAF\Core\\exceptionHandler");

/**
 * Gestion des erreurs.
 *
 * @param int     $no   Type de l'erreur.
 * @param string  $str  Message de l'erreur.
 * @param string  $file Nom du fichier.
 * @param int     $line Numéro de ligne.
 *
 * @return null
 */
function errorHandler ($no, $str, $file, $line)
{
    $throw = false;
    $error = 'ERROR';

    switch ($no) {
    case E_ERROR:
        $throw = true;
        break;

    case E_WARNING:
        $error = 'E_WARNING';
        break;

    case E_PARSE:
        $throw = true;
        break;

    case E_NOTICE:
        $error = 'E_NOTICE';
        break;

    case E_CORE_ERROR:
        $throw = true;
        break;

    case E_CORE_WARNING:
        $error = 'E_CORE_WARNING';
        break;

    case E_COMPILE_ERROR:
        $throw = true;
        break;

    case E_COMPILE_WARNING:
        $error = 'E_COMPILE_WARNING';
        break;

    case E_USER_ERROR:
        $throw = true;
        break;

    case E_USER_WARNING:
        $error = 'E_USER_WARNING';
        break;

    case E_USER_NOTICE:
        $error = 'E_USER_NOTICE';
        break;

    case E_STRICT:
        $error = 'E_STRICT';
        break;

    case E_RECOVERABLE_ERROR:
        $throw = true;
        break;

    case E_DEPRECATED:
        $error = 'E_DEPRECATED';
        break;

    case E_USER_DEPRECATED:
        $error = 'E_USER_DEPRECATED';
        break;
    }

    if ($throw) {
        throw new \ErrorException($str, $no, 0, $file, $line);
    } else {
        echo "<b>$error [$no]</b>: $str<br/>\n";
        echo " $file:$line<br/>\n";
    }
}

set_error_handler("\SWAF\Core\\errorHandler");

?>
