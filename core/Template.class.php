<?php
/**
 * Fichier de la classe Template.
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
 * Classe de génération de template.
 *
 * Syntaxe des template
 * ====================
 *
 * Les fichiers template sont pensé pour garder du code html le plus propre
 * possible, les structures de bloc utilisent donc les balises de commentaire.
 *
 * Variable simple
 * ---------------
 *
 * Les variables se présente sous la forme `${nom_de_la_variable}`. Celles-ci
 * peuvent être composée, par exemple `${var1.var2.var3}`.
 *
 * L'affichage du code
 *
 *     <p>
 *         ${SWAF.version}<br/>
 *         ${test}
 *     </p>
 *
 * Donnerait quelque chose comme ceci
 *
 *     <p>
 *     <?php
 *         echo isset($var['SWAF']['version']) ? $var['SWAF']['version'] : '';
 *     ?><br/>
 *     <?php
 *         echo isset($var['test']) ? $var['test'] : '';
 *     ?>
 *     </p>
 *
 * Condition
 * ---------
 *
 * Il est possible d'effectuer des conditions à l'aide d'une balise de bloc
 * `<!-- IF condition -->`. Les conditions peuvent contenir autant de variables
 * simple ou de bloc que souhaité et être aussi complexe que le permet le php.
 * Il faut terminer la condition avec la balise `<!-- END -->`. Il est également
 * possible d'effectuer un sinon avec la balise `<!-- ELSE -->`.
 *
 * L'affichage du code
 *
 *     <!-- IF ${var1} > ${var2} -->
 *         <!-- IF ${test} -->
 *             test
 *         <!-- ELSE -->
 *             no test
 *         <!-- END -->
 *     <!-- END -->
 *
 * Donnerait quelque chose comme ceci
 *
 *     <?php
 *         if (
 *             (isset($var['var1']) ? $var['var1'] : false) >
 *             (isset($var['var2']) ? $var['var2'] : false)
 *         ) {
 *             if ((isset($var['var']) ? $var['var'] : false)) {
 *                 ?> test <?php
 *             } else {
 *                 ?> no test <?php
 *             }
 *         }
 *     ?>
 *
 * Boucle
 * ------
 *
 * Les bouclent se font à l'aide des bloc `<!-- BLOCK var -->` où `var` est un
 * tableau qui sera parcourut de 0 à n. Les boucle se terminent par
 * `<!-- ENDBLOCK -->`.
 *
 * > **Variable de boucle** : Les sous variables, des variables sur lesquelles
 *   on boucle peuvent être accessible avec la syntaxe
 *   `${variable}{sous-variable}`.
 *
 * ---
 *
 * Il est également possible de définir une balise `<!-- EMPTY -->` pour le cas
 * ou le tableau ne serait pas définit, ne serait pas un tableau ou serait vide.
 *
 * L'affichage du code
 *
 *     <!-- BLOCK ${test} -->
 *         ${test}{subVar}
 *     <!-- EMPTY -->
 *         vide ...
 *     <!-- ENDBLOCK -->
 *
 * Donnerait quelque chose comme ceci
 *
 *     <?php
 *         if (isset($var['test'])) {
 *             for ($_test = 0; $_test < count($var['test']); $_test++) {
 *                 echo isset($var['test'][$_test]['subVar']) ?
 *                     $var['test'][$_test]['subVar'] :
 *                     ''
 *                 ;
 *             }
 *         } else if (!isset($var['test']) ||
 *                    !is_array($var['test']) ||
 *                    count($var['test']) == 0) {
 *             ?> vide ... <?php
 *         }
 *     ?>
 *
 * Bien évidement rien n'empèche d'avoir des sous boucles avec des sous-sous
 * variables.
 *
 * Par exemple, le code
 *
 *     <!-- BLOCK ${a} -->
 *         <!-- BLOCK ${a}{b} -->
 *             ${a}{b}{c}
 *         <!-- ENDBLOCK -->
 *     <!-- ENDBLOCK -->
 *
 * Donnerait quelque chose comme ceci
 *
 *     <?php
 *     if (isset($var['a'])) { for ($_a = 0; $_a < count($var['a']); $_a++) {
 *         if (isset($var['a'][$_a]['b'])) {
 *             for ($_a_b = 0; $_a_b < count($var['a'][$_a]['b']); $_a_b++) {
 *                 echo isset($var['a'][$_a]['b'][$_a_b]['c']) ?
 *                     $var['a'][$_a]['b'][$_a_b]['c'] :
 *                     ''
 *                 ;
 *             }
 *         }
 *     }}
 *     ?>
 *
 * Condition de boucle
 * -------------------
 *
 * Il est également possible de placer certaines conditions dans les boucles.
 * Les voici :
 *
 * - `<!-- FIRST var -->` - Pour tester si c'est la première itération.
 * - `<!-- LAST var -->`  - Pour tester si c'est la dernière itération.
 * - `<!-- EVEN var -->`  - Pour tester si c'est une itération paire.
 * - `<!-- ODD var -->`   - Pour tester si c'est une itération impaire.
 *
 * Bien sur ces conditions se terminent par `<!-- END -->` et peuvent utilisé
 * une balise `<!-- ELSE -->`.
 *
 * Par exemple, le code
 *
 *     <!-- BLOCK ${test} -->
 *         <!-- FIRST ${test} -->
 *             Premier :
 *         <!-- END -->
 *             ${test}{a}
 *     <!-- ENDBLOCK -->
 *
 * Donnerait quelque chose comme ceci
 *
 *     <?php
 *         if (isset($var['test'])) {
 *             for ($_test = 0; $_test < count($var['test']); $_test++) {
 *                 if ($_test == 0) {
 *                     ?> Premier : <?php
 *                 }
 *                 echo isset($var['test'][$_test]['a']) ?
 *                     $var['test'][$_test]['a'] :
 *                     ''
 *                 ;
 *             }
 *         }
 *     ?>
 *
 * ---
 *
 * Le code
 *
 *     <!-- LAST ${test} -->
 *
 * Donnerrait
 *
 *     <?php if ($_test == count($var['test']) - 1) { ?>
 *
 * ---
 *
 * Le code
 *
 *     <!-- EVEN ${test} -->
 *
 * Donnerrait
 *
 *     <?php if ($_test % 2 == 1) { ?>
 *
 * ---
 *
 * Et le code
 *
 *     <!-- ODD ${test} -->
 *
 * Donnerrait
 *
 *     <?php if ($_test % 2 == 0) { ?>
 *
 * Inclusion de fichier
 * --------------------
 *
 * L'inclusion de fichier se fait simplement à l'aide de la balise
 * `<!-- INCLUDE nom_du_fichier -->`. Note que ceci se fera via le gestionnaire
 * de vue qui cherchera ce fichier selon le style courant et le module courant.
 *
 * Par exemple, le code
 *
 *     <!-- INCLUDE header.html -->
 *
 * Donnerait quelque chose comme ceci
 *
 *     <?php ViewManager::display('header.html'); ?>
 *
 * ---
 *
 * @category Framework
 * @package  SWAF\Core
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
    const REG_VAR = '\$((\{(\.?[[:alnum:]_]+)+\})+)';
    /**
     * Expression régulière pour un bloc.
     *
     * @var string
     */
    const REG_BLOCK = '<!-- *([[:alpha:]]+) *(((?!-->).)*) *-->';

    /**
     * Tableau des variables pour l'affichage des fichiers.
     *
     * @var array
     */
    private $_vars = array(
        'SWAF'     => array(
            'version' => SWAF_VERSION
        ),
        'TEMPLATE' => array(
            'version' => '0.1'
        )
    );
    /**
     * Tableau des fonctions de remplacement des blocs.
     *
     * @var array
     */
    private $_replaceBlockFunctions = array(
        'IF'       => '_replaceIf',
        'ELSE'     => '_replaceElse',
        'END'      => '_replaceEnd',
        'BLOCK'    => '_replaceBlock',
        'FIRST'    => '_replaceFirst',
        'LAST'     => '_replaceLast',
        'EVEN'     => '_replaceEven',
        'ODD'      => '_replaceOdd',
        'EMPTY'    => '_replaceEmpty',
        'ENDBLOCK' => '_replaceEndBlock',
        'INCLUDE'  => '_replaceInclude',
        ''         => ''
    );

    /**
     * Définit une variable pour l'affichage des fichiers.
     *
     * @param string $varName Nom de la variable à assigner.
     * @param mixed  $var     Valeur à assigner à la variable.
     *
     * @return bool `true` si la variable était déjà définie, `false` sinon.
     */
    public function setVar ($varName, $var)
    {
        $is = isset($this->_vars[$varName]);
        $this->_vars[$varName] = $var;
        return $is;
    }
    /**
     * Définit des variables pour l'affichage des fichiers.
     *
     * @param array $vars Tableau des variables à assigner.
     *
     * @return null
     */
    public function setVars ($vars)
    {
        foreach ($vars as $varName => $var) {
            $this->_vars[$varName] = $var;
        }
    }
    /**
     * Supprime une variable pour l'affichage des fichiers.
     *
     * @param string $varName Nom de la variable.
     *
     * @return bool `true` si la variable était déjà définie, `false` sinon.
     */
    public function unsetVar ($varName)
    {
        $is = isset($this->_vars[$varName]);
        unset($this->_vars[$varName]);
        return $is;
    }
    /**
     * Vérifie qu'un variable est assignée.
     *
     * @param string $varName Nom de la variable à effacer.
     *
     * @return bool `true` si la variable est assignée, `false` sinon.
     */
    public function issetVar ($varName)
    {
        return isset($this->_vars[$varName]);
    }
    /**
     * Retourne une variable de l'affichage des fichiers.
     *
     * @param string $varName Nom de la variable à effacer.
     *
     * @return mixed Valeur de la variable ou `null` si celle-ci n'est pas
     *               définie.
     */
    public function getVar ($varName)
    {
        if (!isset($this->_vars[$varName])) {
            return null;
        }
        return $this->_vars[$varName];
    }
    /**
     * Affiche et génère si nécessaire un fichier template.
     *
     * @param string $fileName Nom du fichier à afficher.
     *
     * @return null
     * @throws FileException Si le fichier ne peut être généré.
     */
    public function display ($fileName)
    {
        if (!FileManager::checkForCache($fileName)) {
            $this->_generate(realpath($fileName));
        }

        include FileManager::cachepath($fileName).'.php';
    }

    /**
     * Génère le code à partir d'un fichier template et le sauvegarde en cache.
     *
     * @param string $fileName Nom du fichier à afficher.
     *
     * @return null
     * @throws FileException Si le fichier n'existe pas ou ne peut être lu, ou
     *                       que le fichier cache ne peut être enregistré.
     */
    private function _generate ($fileName)
    {
        if (!file_exists($filename)) {
            throw new FileException($fileName, FileException::EXIST);
        }
        if (!is_readable($filename)) {
            throw new FileException($fileName, FileException::READ);
        }

        $handle = fopen($fileName, 'rb');
        $html = fread($handle, filesize($fileName));
        fclose($handle);

        $html = $this->_replaceBlocks($html);
        $html = $this->_replaceVars($html);

        $html = ' * '.date('r')."\n */ ?>\n$html";
        $html = "<?php\n/* generated by SWAF\Core\Template\n$html";

        $html = preg_replace_callback(
            '#\?>(( |\n|\t|\r|)*)<\?php#',
            function ($matches) {
                return $matches[0];
            },
            $html
        );
        // Enregistrement en cache
        $cache = FileManager::cachepath($fileName).'.php';
        if (!is_writable($cache)) {
            throw new FileException($cache, FileException::WRITE);
        }
        $handle = fopen($cache, 'wb');
        fwrite($handle, $html);
        fclose($handle);
    }
    /**
     * Fonction de remplacement des blocs template.
     *
     * @param string $tpl Template à modifier.
     *
     * @return string Le code généré.
     */
    private function _replaceBlocks ($tpl)
    {
        return preg_replace_callback(
            '#'.self::REG_BLOCK.'#',
            function ($matches) {
                $blockType = isset($matches[1]) ? strtoupper($matches[1]) : '';
                $exp = isset($matches[2]) ? $matches[2] : '';
                $func = $this->_replaceBlockFunctions[$blockType];
                if ($func == '') {
                    return '';
                }
                return $this->$func($exp);
            },
            $tpl
        );
    }
    /**
     * Fonction de remplacement des variables template.
     *
     * @param string $tpl  Template à modifier.
     * @param bool   $echo Permet d'afficher directement les variables.
     *
     * @return string Le code généré.
     */
    private function _replaceVars ($tpl, $echo = true)
    {
        return preg_replace_callback(
            '#'.self::REG_VAR.'#',
            function ($matches) use ($echo) {
                $var = $this->_var($matches[1]);
                if (!$echo) {
                    return "(isset($var) ? $var : false)";
                }
                return "<?php echo isset($var) ? $var : ''; ?>";
            },
            $tpl
        );
    }
    /**
     * Fonction de remplacement d'une variable template.
     *
     * @param string $var Variable sous forme de template.
     *
     * @return string Variable dans le contexte.
     */
    private function _var ($var)
    {
        $subVars = explode('{', $this->_clearVar($var));
        $base    = $subVars[0];
        $realVar = $this->_baseVar($base);

        for ($i = 1; $i < count($subVars); $i++) {
            $realVar .= '['.$this->_varCounter($base).']';
            $realVar .= $this->_baseVar($subVars[$i]);
            $base    .= '.'.$subVars[$i];
        }
        return '$this->_vars'.$realVar;
    }
    /**
     * Fonction de remplacement d'une variable template simple.
     *
     * @param string $var Variable sous forme de template simple.
     *
     * @return string Base du nom de la variable dans le contexte.
     */
    private function _baseVar ($var)
    {
        $baseVar = str_replace('.', "']['", $var);
        return "['$baseVar']";
    }
    /**
     * Nettoie une variable template en vue d'être traité par les autres
     * fonctions de remplacement.
     *
     * @param string $var Variable sous forme de template.
     *
     * @return string variable nettoyé.
     */
    private function _clearVar ($var)
    {
        return preg_replace('#(^\$?\{)|\}| #', '', $var);
    }
    /**
     * Retourne le compteur correspondant à une variable template.
     *
     * @param string $var Variable sous forme de template.
     *
     * @return string Compteur.
     */
    private function _varCounter ($var)
    {
        $c = preg_replace('#\{|\.#', '_', $this->_clearVar($var));
        return "\$_$c";
    }
    /**
     * Fonction de remplacement d'un bloc `IF`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceIf ($exp)
    {
        $condition = $this->_replaceVars($exp, false);
        return "<?php if ($condition) { ?>";
    }
    /**
     * Fonction de remplacement d'un bloc `ELSE`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceElse ($exp)
    {
        return '<?php } else { ?>';
    }
    /**
     * Fonction de remplacement d'un bloc `END`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceEnd ($exp)
    {
        return '<?php } ?>';
    }
    /**
     * Fonction de remplacement d'un bloc `BLOCK`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceBlock ($exp)
    {
        $var = $this->_var($exp);
        $i   = $this->_varCounter($exp);
        return "<?php if (isset($var)) {
                for ($i = 0; $i < count($var); $i++) { ?>"
        ;
    }
    /**
     * Fonction de remplacement d'un bloc `FIRST`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceFirst ($exp)
    {
        $i = $this->_varCounter($exp);
        return "<?php if ($i == 0) { ?>";
    }
    /**
     * Fonction de remplacement d'un bloc `LAST`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceLast ($exp)
    {
        $var = $this->_var($exp);
        $i   = $this->_varCounter($exp);
        return "<?php if ($i == count($var) - 1) { ?>";
    }
    /**
     * Fonction de remplacement d'un bloc `EVEN`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceEven ($exp)
    {
        $i = $this->_varCounter($exp);
        return "<?php if ($i % 2 == 1) { ?>";
    }
    /**
     * Fonction de remplacement d'un bloc `ODD`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceOdd ($exp)
    {
        $i = $this->_varCounter($exp);
        return "<?php if ($i % 2 == 0) { ?>";
    }
    /**
     * Fonction de remplacement d'un bloc `EMPTY`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceEmpty ($exp)
    {
        $var = $this->_var($exp);
        $cond  = "<?php }} if (!isset($var)";
        $cond .= " || !is_array($var)";
        $cond .= " || count($var) == 0) {{ ?>";
        return $cond;
    }
    /**
     * Fonction de remplacement d'un bloc `ENDBLOCK`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceEndBlock ($exp)
    {
        return '<?php }} ?>';
    }
    /**
     * Fonction de remplacement d'un bloc `INCLUDE`.
     *
     * @param string $exp Expression du bloc.
     *
     * @return string Code de remplacement.
     */
    private function _replaceInclude ($exp)
    {
        return '<?php \SWAF\Core\ViewManager::display(\''.$exp.'\') ?>';
    }
}
?>
