<?php
/**
 * Fichier de la classe Controller.
 *
 * PHP version 5
 *
 * @category Framework
 * @package  SWAF\Core
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  PHP License 3.01
 * @version  GIT: git://github.com/Maxs1789/SWAF.git
 * @link     https://github.com/Maxs1789/SWAF
 */
namespace SWAF\Core;

/**
 * Contrôleur.
 *
 * @category Framework
 * @package  SWAF\Core
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Controller
{
    /**
     * Module du contrôleur.
     *
     * @var Module
     */
    private $_module;

    /**
     * Constructeur de Contrôleur.
     *
     * @param Module $module Module du contrôleur.
     *
     * @return null
     */
    public function __construct (&$module)
    {
        $this->_module = $module;
    }

    /**
     * Retourne une référence du module du contrôleur.
     *
     * @return Module Référence du module.
     */
    protected function &module ()
    {
        return $this->_module;
    }
    /**
     * Assigne une variable de vues.
     *
     * @param string $varName Nom de la variable.
     * @param mixed  $var     Valeur de la variable.
     *
     * @return null
     */
    protected function setVar ($varName, $var)
    {
        ViewManager::setTemplateVar($varName, $var);
    }
    /**
     * Assigne des variables de vues.
     *
     * @param array $vars Tableau des variables.
     *
     * @return null
     */
    protected function setVars ($vars)
    {
        ViewManager::setTemplateVars($vars);
    }
    /**
     * Supprime une variable de vues.
     *
     * @param string $varName Nom de la variable.
     *
     * @return null
     */
    protected function unsetVar ($varName)
    {
        ViewManager::unsetTemplateVar($varName);
    }
    /**
     * Vérifie qu'un variable de vues est assignée.
     *
     * @param string $varName Nom de la variable.
     *
     * @return bool true si elle est assignée, false sinon.
     */
    protected function issetVar ($varName)
    {
        return ViewManager::issetTemplateVar($vaName);
    }
    /**
     * Retourne la valeur d'une variable de vues.
     *
     * @param string $varName Nom de la variable.
     *
     * @return mixed Valeur de la variable.
     */
    protected function getVar ($varName)
    {
        return ViewManager::getTemplateVar($varName);
    }
}

?>
