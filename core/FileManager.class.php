<?php
/**
 * Fichier de la classe FileManager.
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
 * Classe utilitaire pour la gestion des fichiers cache.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class FileManager
{
    /**
     * Retourne le chemin du fichier cache équivalent à un fichier donné.
     *
     * @param string $fileName Nom du fichier.
     *
     * @return string Le chemin du fichier cache.
     */
    public static function cachepath ($fileName)
    {
        $cache = realpath($fileName);
        if (strpos($cache, MAIN_DIR) !== false) {
            $cache = str_replace(MAIN_DIR, CACHE_DIR, $cache);
        } else {
            $cache = str_replace(':', '', $cache);
            $cache = CACHE_DIR . DIRECTORY_SEPARATOR . $cache;
        }
        return $cache;
    }

    /**
     * Vérifie si l'on peut charger un fichier cache.
     *
     * @param string  $fileName Nom basique du fichier.
     * @param bool    $mkdir    Si à true, crée le dossier cache si nécessaire.
     *
     * @return bool true si le fichier cache peut être chargé, false sinon.
     */
    public static function checkForCache ($fileName, $mkdir = true)
    {
        $base  = realpath($fileName);
        $cache = self::cachepath($fileName);

        $baseTime  = file_exists($base)  ? filemtime($base)  : 0;
        $cacheTime = file_exists($cache) ? filemtime($cache) : 0;

        if ($cacheTime <= $baseTime) {
            if (!file_exists(dirname($cache)) && $mkdir) {
                mkdir(dirname($cache), 0777, true);
            }
            return false;
        }

        return true;
    }
}

?>
