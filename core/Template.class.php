<?php
/**
 * Fichier de la classe Template.
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
 * Classe de génération de template.
 *
 * @category Framework
 * @package  SWAF
 * @author   Van den Branden Maxime <max.van.den.branden@gmail.com>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 0.1
 * @link     https://github.com/Maxs1789/SWAF
 */
class Template
{
    /**
     * Expression régulière pour une variable.
     *
     * @var string
     */
    const REG_MATCH_VAR       = '#\$((\{(\.?[[:alnum:]_]+)+\})+)#';
    /**
     * Expression régulière pour un bloc.
     *
     * @var string
     */
    const REG_MATCH_BLOCK     = '#<!-- *([A-Z]+) *(((?!-->).)*) +-->#';
    /**
     * Expression régulière pour une variable de bloc.
     *
     * @var string
     */
    const REG_MATCH_BLOCK_VAR = '#\$((\[(\.?[[:alnum:]_]+)+\])+)#';

    /**
     * Tableau des variables pour la génération.
     *
     * @var array
     */
    private $_vars;
    /**
     * Dossier courant de travail.
     *
     * @var string
     */
    private $_wrkDir;

    /**
     * Constructeur de Template.
     *
     * @param string $directory Dossier courant de travail.
     *
     * @return null
     */
    public function __construct ($directory = MAIN_DIR)
    {
        $this->_vars = array(
            'SWAF'     => array('version' => SWAF_VERSION),
            'TEMPLATE' => array('version' => '0.1-12-10')
        );
        $this->_wrkDir = $directory;
    }

    /**
     * Assigne une variable pour la génération.
     *
     * @param string $varName Nom de la variable à assigner.
     * @param mixed  $var     Valeur à assigner à la variable.
     *
     * @return null
     */
    public function assignVar ($varName, $var)
    {
        $this->_vars[$varName] = $var;
    }

    /**
     * Assigne des variables pour la génération.
     *
     * @param array $vars Tableau des variables à assigner.
     *
     * @return null
     */
    public function assignVars ($vars)
    {
        foreach ($vars as $varName => $var) {
            $this->_vars[$varName] = $var;
        }
    }

    /**
     * Efface une variable.
     *
     * @param string $varName Nom de la variable à effacer.
     *
     * @return null
     */
    public function clearVar ($varName)
    {
        unset($this->_vars[$varName]);
    }

    /**
     * Génère si nécessaire et affiche le fichier template.
     *
     * @param string $fileName Nom du fichier à afficher.
     *
     * @return null
     */
    function show ($fileName)
    {
        $fileName = $this->_wrkDir."/$fileName";

        if (!FileManager::checkForCache($fileName)) {
            $this->_generate(realpath($fileName));
        }

        include FileManager::cachepath($fileName);
    }

    /**
     * Génère le fichier template.
     *
     * @param string $fileName Nom du fichier à afficher.
     *
     * @return null
     * @throw FileException Si le fichier ne peut être lu.
     */
    private function _generate ($fileName)
    {
        FileException::checkReadable($fileName);

        $handle = fopen($fileName, 'rb');
        $html = fread($handle, filesize($fileName));
        fclose($handle);

        // VAR
        while (preg_match(self::REG_MATCH_VAR, $html, $matches)) {
            $var = $this->_getVar($matches[1]);
            $html = str_replace($matches[0], $this->_getEcho($var), $html);
        }

        // BLOCKS
        while (preg_match(self::REG_MATCH_BLOCK, $html, $matches)) {
            $replace = '';
            $args = $matches[2];

            switch ($matches[1]) {
            case 'IF':
                $replace = $this->_getIf($args);
                break;

            case 'BLOCK':
                $replace = $this->_getFor($args);
                break;

            case 'NOBLOCK':
                $replace = $this->_getNoBlock($args);
                break;

            case 'IFFIRST':
                $i = $this->_getCounter($args);
                $replace = "<?php if($i==0){ ?>";
                break;

            case 'IFLAST':
                $i = $this->_getCounter($args);
                $var = $this->_getVar($args);
                $replace = "<?php if($i==count($var)-1){ ?>";
                break;

            case 'IFEVEN':
                $i = $this->_getCounter($args);
                $replace = "<?php if($i%2==1){ ?>";
                break;

            case 'IFODD':
                $i = $this->_getCounter($args);
                $replace = "<?php if($i%2==0){ ?>";
                break;

            case 'ELSE':
                $replace = '<?php }else{ ?>';
                break;

            case 'ENDIF':
                $replace = '<?php } ?>';
                break;

            case 'ENDBLOCK':
                $replace = '<?php }} ?>';
                break;

            case 'INCLUDE':
                $replace = $this->_getInclude($args);
                break;
            }
            $html = str_replace($matches[0], $replace, $html);
        }

        $html = ' * '.date('r')."\n */ ?>\n$html";
        $html = "<?php\n/* generated by SWAF\Core\Template\n$html";

        while (preg_match('#\?>(( |\n|\t|\r|)*)<\?php#', $html, $matches)) {
            $html = str_replace($matches[0], $matches[1], $html);
        }

        $cache = FileManager::cachepath($fileName);
        $handle = fopen($cache, 'wb');
        fwrite($handle, $html);
        fclose($handle);
    }

    /**
     * Retourne le nom d'une variable du contexte de cette classe à partir de
     * son nom simple dans le template.
     *
     * @param string $tplVar Nom de la variable template simple.
     *
     * @return string Nom de la variable dans le contexte.
     */
    private function _getSimpleVar ($tplVar)
    {
        if ($tplVar == '') {
            return '';
        }
        return "['".str_replace('.', "']['", $tplVar)."']";
    }

    /**
     * Retourne le nom d'une variable du contexte de cette classe à partir de
     * son nom dans le template.
     *
     * @param string $tplVar Nom de la variable template.
     *
     * @return string Nom de la variable dans le contexte.
     */
    private function _getVar ($tplVar)
    {
        $vars = preg_split('#(\{|\[)#', $this->_getClearVar($tplVar));
        $base = $vars[0];
        $finalVar = $this->_getSimpleVar($vars[0]);
        for ($i = 1; $i < count($vars); $i++) {
            $finalVar = $finalVar.'['.$this->_getCounter($base).']';
            $finalVar = $finalVar.$this->_getSimpleVar($vars[$i]);
            $base = $base.'.'.$vars[$i];
        }
        return '$this->_vars'.$finalVar;
    }

    /**
     * Retourne le compteur d'une variable du contexte de cette classe à partir
     * de son nom dans le template.
     *
     * @param string $tplVar Nom de la variable template.
     *
     * @return string Compteur de la variable dans ce contexte.
     */
    private function _getCounter ($tplVar)
    {
        $var = $this->_getClearVar($tplVar);
        return '$_'.preg_replace('#(\.|\{|\[)#', '_', $var).'_count';
    }

    /**
     * Nettoie le nom d'une variable template.
     *
     * @param string $tplVar Nom de la variable template.
     *
     * @return string Variable template propre.
     */
    private function _getClearVar ($tplVar)
    {
        $tplVar = preg_replace('#(\$|\}|\]| )#', '', $tplVar);
        return preg_replace('#(^\{|^\[)#', '', $tplVar);
    }

    /**
     * Retourne le code d'affichage d'une variable à partir de son nom.
     *
     * @param string $var Nom de la variable.
     *
     * @return string Code d'affichage.
     */
    private function _getEcho ($var)
    {
        return "<?php echo isset($var)?$var:'<i>null</i>'; ?>";
    }

    /**
     * Retourne le code de boucle d'une variable à partir de son nom.
     *
     * @param string $tplVar Nom de la variable.
     *
     * @return string Code de boucle.
     */
    private function _getFor ($tplVar)
    {
        $var = $this->_getVar($tplVar);
        $i = $this->_getCounter($tplVar);
        return "<?php if(isset($var)){for($i=0;$i<count($var);$i++){ ?>";
    }

    /**
     * Retourne le code de non-boucle d'une variable à partir de son nom.
     *
     * @param string $tplVar Nom de la variable.
     *
     * @return string Code de non-boucle.
     */
    private function _getNoBlock ($tplVar)
    {
        $var = $this->_getVar($tplVar);
        return "<?php }}if(!isset($var)||count($var)==0){{ ?>";
    }

    /**
     * Retourne le code de condition.
     *
     * @param string $condition Condition à tester.
     *
     * @return string Code de condition.
     */
    private function _getIf ($condition)
    {
        while (preg_match(self::REG_MATCH_BLOCK_VAR, $condition, $matches)) {
            $var = $this->_getVar($matches[1]);
            $issetVar = "(isset($var)?$var:false)";
            $condition = str_replace($matches[0], $issetVar, $condition);
        }
        return "<?php if ($condition){ ?>";
    }

    /**
    * Retourne le code d'inclusion d'un fichier à partir de son nom.
    *
    * @param string $fileName Nom du fichier à inclure.
    *
    * @return string Code d'inclusion.
    */
    private function _getInclude ($fileName)
    {
        return "<?php \$this->show('$fileName'); ?>";
    }
}
?>
