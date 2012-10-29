<?php
/**
 * Fichier de la classe ViewManager.
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
 * Gestionnaire de vue.
 *
 * @category Framework
 * @package  SWAF\Core
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class ViewManager
{
    /**
     * Template du module.
     * 
     * @var Template
     */
    private static $_template;
    /**
     * Style courant utilisé par le Gestionnaire de vue.
     * 
     * @var string
     */
    private static $_style = '';
    /**
     * Dossier du style par défaut.
     * 
     * @var string
     */
    private static $_moduleStyleDir = '';

    /**
     * Change le style courant.
     * 
     * @param string $style Nom du style.
     * 
     * @return null
     */
    public static function setStyle ($style)
    {
        self::$_style = $style;
    }
    /**
     * Retourne le nom du style courant.
     * 
     * @return string Nom du style.
     */
    public static function getStyle ()
    {
        return self::$_style;
    }
    /**
     * Change le dossier du style par défaut.
     * 
     * @param string $directory Nom du dossier.
     * 
     * @return null
     */
    public static function setDefaultDirectory ($directory)
    {
        self::$_moduleStyleDir = $directory;
    }
    /**
     * Retourne le dossier du style par défaut.
     * 
     * @return string Nom du dossier.
     */
    public static function getDefaultDirectory ()
    {
        return self::$_moduleStyleDir;
    }
    /**
     * Assigne une variable du générateur de template.
     * 
     * @param string $varName Nom de la variable.
     * @param mixed  $var     Valeur de la variable.
     * 
     * @return null
     */
    public static function setTemplateVar ($varName, $var)
    {
        self::_template()->setVar($varName, $var);
    }
    /**
     * Assigne des variables du générateur de template.
     * 
     * @param array $vars Tableau des variables.
     * 
     * @return null
     */
    public static function setTemplateVars ($vars)
    {
        self::_template()->setVars($vars);
    }
    /**
     * Supprime une variable du générateur de template.
     * 
     * @param string $varName Nom de la variable.
     * 
     * @return null
     */
    public static function unsetTemplateVar ($varName)
    {
        self::_template()->unsetVar($varName);
    }
    /**
     * Vérifie qu'un variable du générateur de template est assignée.
     * 
     * @param string $varName Nom de la variable.
     * 
     * @return bool true si elle est assignée, false sinon.
     */
    public static function issetTemplateVar ($varName)
    {
        return self::_template()->issetVar($vaName);
    }
    /**
     * Retourne la valeur d'une variable du générateur de template.
     * 
     * @param string $varName Nom de la variable.
     * 
     * @return mixed Valeur de la variable.
     */
    public static function getTemplateVar ($varName)
    {
        return self::_template()->getVar($varName);
    }

    /**
     * Affiche un fichier à l'aide du générateur de template.
     * 
     * @param string $fileName Nom du fichier à afficher.
     * 
     * @return null
     * @throws FileException Si aucun fichier n'a pu être trouvé ou lu.
     */
    public static function display ($fileName)
    {
        $name = self::_getFileName($fileName);
        self::_template()->display($name);
    }

    /**
     * Retourne la référence du générateur de template.
     * 
     * @return Template Référence du générateur de template.
     */
    private static function &_template ()
    {
        if (self::$_template == null) {
            self::$_template = new Template();
        }
        return self::$_template;
    }
    /**
     * Vérifie l'existence d'un fichier dans les dossiers de style et retourne 
     * le nom complet du fichier trouvé.
     * 
     * Cette méthode cherche d'abord dans le style courant, si le fichier n'est
     * pas trouvé, elle cherche dans le dossier du style par défaut. Si le 
     * fichier n'est toujours pas trouvé une exception sera lancée.
     * 
     * @param string $fileName Nom du fichier à chercher.
     * 
     * @return string Nom complet du fichier trouvé.
     * @throws FileException Si aucun fichier n'a pu être trouvé ou lu.
     */
    private static function _getFileName ($fileName)
    {
        // Style courant
        if (self::$_style != '') {
            $baseName = MAIN_DIR.'/styles/'.self::$_style.'/'.$fileName;
            $realName = realpath($baseName);
            if ($realName != '' && is_readable($realName)) {
                return $realName;
            }
        }
        // Style par défaut
        $baseName = self::$_moduleStyleDir.'/'.$fileName;
        $realName = realpath($baseName);
        if ($realName == '') {
            throw new FileException($baseName, FileException::EXIST);
        }
        if (!is_readable($realName)) {
            throw new FileException($realName, FileException::READ);
        }
        return $realName;
    }
}

?>
