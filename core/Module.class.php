<?php
/**
 * Fichier de la classe Module.
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
 * Module.
 *
 * @category Framework
 * @package  SWAF\Core
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Module
{
    /**
     * Routeur du module.
     *
     * @var Router
     */
    private $_router;
    /**
     * Tableau des contrôleurs du module.
     *
     * Il se présente sous la forme :
     *
     *     array(
     *         'Controller' => array(
     *             'file' => 'Controller.class.php',
     *             'inst' => new Controller()
     *         ),
     *         ...
     *     )
     *
     * 'Controller' doit correspondre avec le nom de la classe de celui-ci.
     *
     * - 'file' - Fichier contenant la classe du contrôleur.
     * - 'inst' - Instance du contrôleur.
     *
     * @var array
     */
    private $_controllers;
    /**
     * Espace de nom du module.
     *
     * @var string
     */
    private $_namespace;
    /**
     * Dossier du module.
     *
     * @var string
     */
    private $_directory;

    /**
     * Constructeur de Module.
     *
     * @return null
     */
    public function __construct ()
    {
        $this->_router      = new Router();
        $this->_controllers = array();
        $this->_namespace   = '';
        $this->_directory   = MAIN_DIR;
    }

    /**
     * Initialise le module.
     *
     * @param string $directory Dossier du module.
     *
     * @return null
     * @throw FileException Lors d'une erreur de fichier.
     */
    public function init ($directory)
    {
        $this->_directory = realpath($directory);
        $confFile = realpath("$directory/module.ini");

        FileException::checkReadable($confFile);
        $ini = parse_ini_file($confFile, true);
        // Récupération des routes
        if (isset($ini['module']['route_file'])) {
            $routeFile = $ini['module']['route_file'];
            $this->_router->loadFile($directory.'/'.$routeFile);
        } else {
            trigger_error(
                "Fichier de route non spécifié.", 
                E_USER_WARNING
            );
        }
        // Récupération de l'espace de nom
        if (isset($ini['module']['namespace'])) {
            $this->_namespace = $ini['module']['namespace'];
        }
        // Récupération des contrôleurs
        if (isset($ini['controllers'])) {
            foreach ($ini['controllers'] as $name => $file) {
                $this->_controllers[$name] = array(
                    'file' => realpath($directory.'/'.$file),
                    'inst' => null
                );
            }
        } else {
            trigger_error(
                "Aucun contrôleur n'est spécifié.", 
                E_USER_WARNING
            );
        }
    }

    /**
     * Retourne un contrôleur.
     *
     * @param string $controllerName Nom du contrôleur.
     *
     * @return Controller Une référence du contrôleur demandé.
     * @throw CoreException Si le contrôleur n'existe pas.
     */
    public function &controller ($controllerName)
    {
        if (!isset($this->_controllers[$controllerName])) {
            throw new CoreException(
                "Contrôleur '$controllerName' non définit."
            );
        }

        $controller = &$this->_controllers[$controllerName];

        if ($controller['inst'] == null) {
            FileException::checkReadable($controller['file']);
            include_once $controller['file'];
            $className = $this->_namespace.'\\'.$controllerName;
            if (!class_exists($className)) {
                throw new CoreException(
                    "Impossible de trouver la classe '$className'"
                );
            }
            $controller['inst'] = new $className($this);
            if (!($controller['inst'] instanceof \SWAF\Core\Controller)) {
                $controller['inst'] = null;
                throw new CoreException(
                    "'$controllerName' n'est pas un contrôleur."
                );
            }
        }

        return $controller['inst'];
    }

    /**
     * Retrouve la route correspondante à l'url est appel l'action lié.
     *
     * @param string $url L'url à appeler.
     *
     * @return null
     * @throw CoreException Si la route ne peut être appelée ou que le 
     *                      contrôleur n'existe pas.
     */
    public function call ($url)
    {
        $act = $this->_router->getAction($url);

        if ($act == null) {
            throw new CoreException('Page non trouvée.');
        }

        $controllerName = $act['controller'];
        $action = $act['action'];
        $method = $act['method'];
        $controller = &$this->controller($controllerName);

        if (!method_exists($controller, $method)) {
            throw new CoreException(
                "La méthode '$controllerName::$method' n'existe pas."
            );
        }

        if (eval("\$controller->$action;") === false) {
            throw new CoreException(
                "Impossible d'éxécuter l'action '$controllerName->$action'."
            );
        }
    }

    /**
     * Affiche une page du module.
     *
     * @param string $FileName Nom du fichier à afficher.
     *
     * @return null
     */
    public function display ($fileName)
    {
        ViewManager::setDefaultDirectory($this->_directory.'/style');
        ViewManager::display($fileName);
    }
}

?>
