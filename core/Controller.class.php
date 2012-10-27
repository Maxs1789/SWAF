<?php
/**
 * Fichier de la classe Controller.
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
 * Contrôleur.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Controller
{
    /**
     * Générateur de template.
     *
     * @var Template
     */
    private $_template;

    /**
     * Constructeur de Contrôleur.
     *
     * @param string $directory Nom du dossier du module.
     *
     * @return null
     */
    public function __construct ($directory = MAIN_DIR)
    {
        $this->_template = new Template($directory.'/style');
    }

    /**
     * Assigne une variable du générateur de template.
     *
     * @param string $varName Nom de la variable à assigner.
     * @param mixed  $var     Valeur à assigner à la variable.
     *
     * @return null
     */
    public function assignTemplateVar ($varName, $var)
    {
        $this->_template->assignVar($varName, $var);
    }

    /**
     * Assigne des variables du générateur de template.
     *
     * @param array $vars Tableau des variables à assigner.
     *
     * @return null
     */
    public function assignTemplateVars ($vars)
    {
        $this->_template->assignVars($vars);
    }

    /**
     * Efface une variable du générateur de template.
     *
     * @param string $varName Nom de la variable à effacer.
     *
     * @return null
     */
    public function clearTemplateVar ($varName)
    {
        $this->_template->clearVar($varName);
    }

    /**
     * Affiche une page via le générateur de template.
     *
     * @param string $fileName Nom du fichier à afficher.
     *
     * @return null
     */
    public function show ($fileName)
    {
        $this->_template->show($fileName);
    }
}

?>
