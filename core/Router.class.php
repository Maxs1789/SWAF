<?php
/**
 * Fichier de la classe Router.
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

define('VAR_REGEX',    '\{([[:alnum:]]+)\}');
define('ARG_REGEX',    '\$([[:alnum:]]+)');
define('ACTION_REGEX', ' *([[:alnum:]_]+) *\((,? *'.ARG_REGEX.' *)*\)');

/**
 * Gère les routes.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Router
{
    private $_routes;

    /**
     * Constructeur de Router.
     *
     * @return null.
     */
    public function __construct ()
    {
        $this->_routes = array();
    }

    /**
     * Charge un fichier de routes.
     *
     * @param string $fileName Nom du fichier à charger.
     *
     * @return null.
     */
    public function loadFile ($fileName)
    {
        if (FileManager::checkForCache($fileName)) {
            $this->_loadCacheFile(FileManager::cachepath($fileName));
        } else {
            $this->_loadBaseFile(realpath($fileName));
            $this->_saveCacheFile(FileManager::cachepath($fileName));
        }
    }

    /**
     * Retrouve la route correspondante à l'url et retourne le contrôleur et 
     * l'action qui y correspondent.
     *
     * Le tableau est retourné sous la forme :
     * array(
     *     'controller' => nom_du_controleur,
     *     'method'     => nom_de_la_methode,
     *     'action'     => action
     * );
     * 
     * L'action est une string avec tout les paramètres, par exemple 
     * "test('42', 'a')".
     *
     * @param string $url Url.
     *
     * @return l'action si l'url à été trouvé ou null dans le cas contraire.
     */
    public function getAction ($url)
    {
        foreach ($this->_routes as $route) {
            if (preg_match('#^'.$route['regex'].'$#', $url, $matches)) {
                $vars = $matches;
                $action = $route['action'];
                preg_match('# *'.ACTION_REGEX.' *$#', $action, $matches);
                $method = $matches[1];
                $i = 1;
                if (isset($route['vars'])) {
                    foreach ($route['vars'] as $varName => $var) {
                        $action = str_replace(
                            '$'.$varName, '\''.$vars[$i].'\'', $action
                        );
                        $i++;
                    }
                }
                return array(
                    'controller' => $route['controller'],
                    'method'     => $method,
                    'action'     => $action
                );
            }
        }
        return null;
    }

    /**
     * Gènère une url à partir d'une route et de ses arguments.
     *
     * @param string $routeName Nom de la route.
     * @param array  $vars      Tableau des paramètres.
     *
     * @return L'url généré.
     * @throw CoreException Si la route n'est pas trouvée ou que les paramètres 
     *                      sont incorrect.
     */
    public function generate ($routeName, $vars)
    {
        if (!isset($this->_routes[$routeName])) {
            throw new CoreException("Route '$routeName' non définie.");
        }
        $route = $this->_routes[$routeName];
        $url   = $route['pattern']; 
        foreach ($route['vars'] as $varName => $var) {
            if (!isset($vars[$varName])) {
                throw new CoreException(
                    "Le paramètre '$varName' n'est pas donné."
                );
            }
            if (!preg_match('#^('.$var.')$#', $vars[$varName])) {
                throw new CoreException(
                    "Le paramètre '$varName' ne respecte par la règle."
                );
            }
            $url = str_replace('{'.$varName.'}', $vars[$varName], $url);
        }
        return $url;
    }

    /**
     * Charge un fichier de base et vérifie les routes.
     *
     * @param string $fileName Nom du fichier.
     *
     * @return null.
     * @throw FileException Lors d'une erreur de fichier.
     */
    private function _loadBaseFile ($fileName)
    {
        FileException::checkReadable($fileName);
        $ini = parse_ini_file($fileName, true);

        foreach ($ini as $routeName => $route) {
            if (isset($this->_routes[$routeName])) {
                trigger_error(
                    "Multiple définition de la route $routeName. (ignorée)", 
                    E_USER_WARNING
                );
            } else {
                try {
                    $this->_routes[$routeName] = $this->_checkBaseRoute($route);
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
    private function _checkBaseRoute ($route)
    {
        // Vérification de base 
        if (!isset($route['pattern'])) {
            throw new CoreException("Pattern non définis.");
        }        
        if (!isset($route['controller'])) {
            throw new CoreException("Contrôleur non définis.");
        }
        if (!isset($route['action'])) {
            throw new CoreException("Action non définis.");
        }
        // Initialisation des paramètres
        $pattern    = $route['pattern'];
        $vars       = array();
        $regex      = $pattern;
        $controller = $route['controller'];
        $action     = $route['action'];
        // Analyse du pattern et récupération des variables
        preg_match_all('#'.VAR_REGEX.'#', $pattern, $matches);        
        if (isset($matches[1])) {
            foreach ($matches[1] as $var) {
                if (isset($vars[$var])) {
                    throw new CoreException(
                        "Multiple définition de '$var' dans le pattern."
                    );
                } else {
                    $vars[$var] = '.*';
                }
            }
        }
        // Récupération des règles et création de l'expression régulière
        foreach ($vars as $varName => $var) {
            if (isset($route['vars'][$varName])) {
                $vars[$varName] = $route['vars'][$varName];
            }
            $regex = str_replace(
                '{'.$varName.'}', '('.$vars[$varName].')', $regex
            );
        }
        // Contrôle de l'action
        if (!preg_match('#^ *'.ACTION_REGEX.' *$#', $action)) {
            throw new CoreException("Action '$action' incorrecte.");
        }
        preg_match_all('#'.ARG_REGEX.'#', $action, $matches);        
        if (isset($matches[1])) {
            foreach ($matches[1] as $var) {
                if (!isset($vars[$var])) {
                    throw new CoreException("Variable '$var' non définie.");
                }
            }
        }
        // Route finale
        return array(
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $action,
            'regex'      => $regex,
            'vars'       => $vars
        );
    }

    /**
     * Charge un fichier cache.
     *
     * @param string $fileName Nom du fichier.
     *
     * @return null.
     * @throw FileException Lors d'une erreur de fichier.
     */
    private function _loadCacheFile ($fileName)
    {
        FileException::checkReadable($fileName);
        $this->_routes = parse_ini_file($fileName, true);
    }

    /**
     * Sauvegarde un fichier cache.
     *
     * @param string $fileName Nom du fichier.
     *
     * @return null.
     * @throw FileException Lors d'une erreur de fichier.
     */
    private function _saveCacheFile ($fileName)
    {
        FileException::checkWritable($fileName);
        $file = fopen($fileName, 'w');
        fwrite($file, "; generated by SWAF\Core\Router\n");
        fwrite($file, '; '.date('r')."\n");
        foreach ($this->_routes as $routeName => $route) {
            fwrite($file, "[$routeName]\n");
            fwrite($file, "pattern = '".$route['pattern']."'\n");
            fwrite($file, "controller = '".$route['controller']."'\n");
            fwrite($file, "action = '".$route['action']."'\n");
            fwrite($file, "regex = '".$route['regex']."'\n");
            foreach ($route['vars'] as $varName => $var) {
                fwrite($file, "vars[$varName] = '$var'\n");
            }
        }
        fclose($file);
    }
}

?>