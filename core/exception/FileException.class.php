<?php
/**
 * Fichier de la classe FileException.
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
 * Exception de fichier.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class FileException extends CoreException
{
    const EXIST = 0;
    const READ  = 1;
    const WRITE = 2;

    /**
     * Constructeur de FileException.
     *
     * @param string    $fileName Le nom du fichier.
     * @param integer   $code     Code d'erreur.
     * @param Exception $previous Exception précédente.
     *
     * @return null
     */
    public function __construct ($fileName, $code, $previous = null)
    {
        $message = "Erreur rencontré avec le fichier $fileName.";
        switch ($code) {
        case self::EXIST:
            $message = "Le fichier '$fileName' n'existe pas.";
            break;
        case self::READ:
            $message = "Impossible de lire le fichier '$fileName'.";
            break;
        case self::WRITE:
            $message = "Impossible d'enregistrer le fichier '$fileName'.";
            break;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Vérifie l'existence d'un fichier et lance l'exception approprié si 
     * celui-ci n'existe pas.
     *
     * @param string $fileName Le nom du fichier à vérifier.
     *
     * @return null
     * @throw FileException Si le fichier n'existe pas.
     */
    public static function checkExists ($fileName)
    {
        if (!file_exists($fileName)) {
            throw new FileException($fileName, self::EXIST);
        }
    }

    /**
     * Vérifie si un fichier peut être lu et lance une exception si ce n'est pas
     * le cas.
     *
     * @param string $fileName Le nom du fichier à vérifier.
     *
     * @return null
     * @throw FileException Si le fichier ne peut être lu.
     */
    public static function checkReadable ($fileName)
    {
        self::checkExists($fileName);
        if (!is_readable($fileName)) {
            throw new FileException($fileName, self::READ);
        }
    }

    /**
     * Vérifie si il est possible d'écrire dans un fichier et lance une 
     * exception si ce n'est pas le cas.
     *
     * @param string $fileName Le nom du fichier à vérifier.
     *
     * @return null
     * @throw FileException Si on ne peut écrire dans le fichier.
     */
    public static function checkWritable ($fileName)
    {
        if (file_exists($fileName) && !is_writable($fileName)) {
            throw new FileException($fileName, self::WRITE);
        }
    }
}

?>
