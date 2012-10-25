<?php
/**
 * Fichier de la classe Core.
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
 * Coeur du framework.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Core
{
    const LINK_REGEX = '[a-zA-Z0-9/%\-_\.]*';

    private $_modules;
    private $_routes;

    private static $_currentLink;

    /**
     * Constructeur de Core.
     *
     * @return null.
     */
    public function __construct ()
    {
        $this->_modules = array();
        $this->_routes  = array(); 
    }

    /**
     * Initialise le Coeur du framework.
     *
     * @return null.
     */
    public function init ()
    {
        $file = realpath(MAIN_DIR.'/conf.ini');
        $ini = parse_ini_file($file, true);
        if (!isset($ini['route_file'])) {
            throw new CoreException("Fichier des routes non définit.");
        }
        $this->_loadRouteFile($ini['route_file']);
        // Récupération des modules
        if (!isset($ini['modules'])) {
            throw new CoreException("Aucun module définit.");
        }
        foreach ($ini['modules'] as $mod => $dir) {
            $this->_modules[$mod] = array (
                'dir'  => $dir,
                'inst' => null
            );
        }
    }

    /**
     * Appèle une route.
     *
     * Si l'url donnée vaut null, le lien courant sera appelé.
     *
     * @param string $url La route à appeler.
     *
     * @return true si la route à pu être appelée, false sinon.
     */
    public function call ($url = null)
    {
        if ($url == null) {
            $url = $this->currentLink();
        }

        foreach ($this->_routes as $route) {
            if (preg_match('#^'.$route['regex'].'$#', $url, $matches)) {
                $module = &$this->module($route['module']);
                $module->call($matches[1]);
                return true;
            }
        }
        return false;
    }

    /**
     * Retourne la référence d'un module.
     *
     * @param string $moduleName Le nom du module.
     *
     * @return La référence du module.
     */
    public function &module ($moduleName)
    {
        if (!isset($this->_modules[$moduleName])) {
            throw new CoreException(
                "Module '$moduleName' non définit."
            );
        }

        $module = &$this->_modules[$moduleName];

        if ($module['inst'] == null) {
            $module['inst'] = new Module();
            try {
                $module['inst']->init($module['dir']);
            } catch (FileException $e) {
                $module['inst'] = null;
                throw $e;
            }
        }

        return $module['inst'];
    }

    /**
     * Retourne le lien courant.
     *
     * @return Le lien courant.
     */
    public static function currentLink ()
    {
        if (self::$_currentLink == null) {
            $root = realpath($_SERVER['DOCUMENT_ROOT']);
            $main_route = str_replace($root, '', MAIN_DIR);
            // Pour windows
            $main_route = str_replace('\\', '/', $main_route);
            $uri = $_SERVER['REQUEST_URI'];
            preg_match(
                '#'.$main_route.'('.self::LINK_REGEX.')#', $uri, $matches
            );
            self::$_currentLink = $matches[1] == '' ? '/' : $matches[1];
        }
        return self::$_currentLink;
    }

    /**
     * Charge le fichier des routes principale.
     *
     * @param string $fileName Nom du fichier.
     *
     * @return null.
     * @throw FileException Si le fichier ne peut être lu.
     */
    private function _loadRouteFile ($fileName)
    {
        $file = realpath($fileName);
        FileException::checkReadable($file);
        $ini  = parse_ini_file($file, true);
        foreach ($ini as $routeName => $route) {
            if (isset($this->_routes[$routeName])) {
                trigger_error(
                    "Multiple définition de la route $routeName. (ignorée)",
                    E_USER_WARNING
                );
            } else {
                try {
                    $this->_routes[$routeName] = $this->_checkRoute($route);
                } catch (CoreException $e) {
                    trigger_error(
                        "La route $routeName n'est pas correcte. (ignorée)", 
                        E_USER_WARNING
                    );
                }
            }
        }
    }

    /**
     * Vérifie une route.
     *
     * @param array $route Tableau de la route.
     *
     * @return La route complètée et vérifiée.
     * @throw CoreException Si la route est mal définie.
     */
    private function _checkRoute ($route)
    {
        // Vérification de base 
        if (!isset($route['pattern'])) {
            throw new CoreException("Pattern non définis.");
        }
        if (!isset($route['module'])) {
            throw new CoreException("Module non définis.");
        }

        $route['regex'] = $route['pattern'].'(.*)';
        return $route;
    }
}

?>
