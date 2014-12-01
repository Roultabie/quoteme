<?php
/**
 * Timply class
 *
 * Genarate webpage from html sources
 *
 * @package     Timply
 * @author      Daniel Douat <daniel@gorgones.net>
 * @link        http://daniel.douat.fr
 */
class timply
{
    private static $themeDir;
    private static $dictionary;
    private $blockList;
    private $file;
    private $firstElement;
    public  $block;
    public  $fileName;

    function __construct($fileName)
    {
        $this->setFileName($fileName);
        $this->setFile();
        $this->includeFiles();
        $this->setBlockList();
    }


    /**
     * Load theme uri
     * @param string $uri dir of theme
     */
    public static function setUri($uri)
    {
        if (!empty($uri)) {
            rtrim($uri, '/');
            $uri = $uri . '/';
        }
        self::$themeDir = $uri;
    }

    /**
     * Create an array with element name to replace by datas
     * @access   public
     * @param    string element name to replace
     * @param    string datas
     * @param    string bock name if elements to replace are in a loop
     * @return   void
     */
    public function setElement($element, $data, $block = "")
    {
        (!empty($block)) ? $this->setBlock($element, $data, $block) : $this->element[$element] = $data;
    }

    /**
     * For elements in a loop. Give him an array and it works for you
     * @access   public
     * @param    array $data  $array[] = array('elementName' => 'elementValue');
     * @param    string $block block name of elements
     */
    public function setElements($datas, $block)
    {
        if (is_array($datas) && !empty($block)) {
            foreach ($datas as $elements) {
                foreach ($elements as $element => $data) {
                    $this->setElement($element, $data, $block);
                }
            }
        }
    }

    /**
     * Return the result in HTML
     * @access   public
     * @return   string html result
     */
    public function returnHtml()
    {
        $this->addBlock();
        $this->addElement();
        $this->traduct();
        $this->cleanFile();
        return $this->getFile();
    }

    /**
     * Load new dictionary
     * @param string $file php file of dictionary, format like $lang['word'] = mot
     */
    public static function addDictionary($file)
    {
        if (file_exists($file)) {
            include $file;
            if (!is_array(self::$dictionary)) {
                self::$dictionary = array();
            }
            if (is_array($lang)) {
                self::$dictionary = array_merge(self::$dictionary, $lang);
            }
        }
    }

    // End # public functions -------------------------------------------------

    // Start # private functions ----------------------------------------------

    /**
     * Create an array with elements replaced by datas only for blocks
     * @access   private
     * @param    string element name to replace
     * @param    string datas
     * @param    string bock name
     * @return   void
     */
    private function setBlock($element, $data, $block)
    {
        if ((empty($this->block[$block])) || ($element == $this->firstElement)) {
                $this->block[$block] .= $this->getBlock($block);
                $this->firstElement   = $element;
            }
            $this->block[$block] = str_replace('{' . $element . '}', $data, $this->block[$block]);
    }

    /**
     * Replace elements of block (created by setBlockList function) by datas presents on $this->block array
     * @access   private
     * @return   void
     */
    private function addBlock()
    {
        if (is_array($this->block)) {
            $file = $this->getFile();
            foreach ($this->block as $key => $data) {
               $file = str_replace('[' . $key . ']', $data, $file);
            }
            $this->setFile($file);
        }
    }

    /**
     * Replace elements from datas presents on $this->element array
     * @access   private
     * @return   void
     */
    private function addElement()
    {
        if (is_array($this->element)) {
            foreach ($this->element as $key => $data) {
                $file = $this->getFile();
                $this->setFile(str_replace('{' . $key . '}', $data, $file));
            }
        }
    }

    /**
     * Return html elements from blocks presents on template
     * @access   private
     * @param    string block name
     * @return   string html structure from blocks
     */
    private function getBlock($blockName)
    {
        return $this->blockList[$blockName];
    }

    /**
     * Return current content state of data file
     * @return   string current data state
     */
    private function getFile()
    {
        return $this->file;
    }

    /**
     * Create a string from templates / replaces the string with the string processed
     * @access   private
     * @param    string datas processed
     * @return   void
     */
    private function setFile($datas = "")
    {
        if (empty($datas)) {
            if (file_exists(self::$themeDir . $this->fileName)) {
                $this->file = file_get_contents(self::$themeDir . $this->fileName);
            }
        }
        else {
            $this->file = $datas;
        }
    }

    /**
     * Load file of them
     * @param string $fileName filename
     */
    private function setFileName($fileName)
    {
        if (!empty($fileName)) {
            $this->fileName = $fileName;
        }
    }

    /**
     * Add content of external files tagged like <!-- Include myfile.ext -->
     * @access   private
     * @return   void
     */
    private function includeFiles()
    {
        $file    = $this->getFile();
        $pattern = "|(<!-- Include (?P<include>[\w\d]+\.[\w\d]+) -->)|iU";
        preg_match_all($pattern, $file, $matches);
        if (is_array($matches['include'])) {
            foreach ($matches['include'] as $key => $toInclude) {
                if (file_exists(self::$themeDir . $toInclude)) {
                    $content = file_get_contents(self::$themeDir . $toInclude);
                    $file    = str_replace($matches[0][$key], $content, $file);
                }
                else {
                    $file = str_replace($matches[0][$key], '', $file);
                }
            }
        }
        $this->setFile($file);
    }

    /**
     * Create a blocklist array with datas / elements to replace and rewrite file with blockname instead of elements
     * @access   private
     * @return   void
     */
    private function setBlockList()
    {
        $pattern = "|(<!-- Start (?P<blockName>[\w\d]+) -->)(?P<blockElements>.*?)(<!-- End \\2 -->)|ismU";
        preg_match_all($pattern, $this->getFile(), $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            // Example : for <!-- Start Content --> block, return $this->blocks['Content'] = "<p>Hello world !</p>";
            $this->blockList[$matches['blockName'][$i]] = trim($matches['blockElements'][$i]);
            // and replace Content block by [Content] flag in the file.
            $this->setFile(str_replace($matches[0][$i], "[" . $matches['blockName'][$i]. "]", $this->getFile()));
        }
    }

    /**
     * Remove working tags from content
     * @return   void
     */
    private function cleanFile()
    {
        $file     = $this->getFile();
        $patterns = array('/{[\d\w\-_]+}/', '/\[[\d\w\-_]+\]/');
        $file     = preg_replace($patterns, '', $file);
        $this->setFile($file);
    }

    /**
     * Replace [trad::] blocks by dictionary entries]
     * @return void
     */
    private function traduct()
    {
        if (is_array(self::$dictionary)) {
            mb_internal_encoding('UTF-8');
            $file    = $this->getFile();
            $pattern = '/\[trad::([\d\w\-_]+):{0,2}(F|AF|A){0,1}\]/';
            preg_match_all($pattern, $file, $matches);
            $count   = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $string = self::$dictionary[$matches[1][$i]];
                if (!empty($string)) {
                    switch ($matches[2][$i]) {
                        case 'F':
                            // Not using ucfirst to preserve locales
                            $traduction = mb_strtoupper(mb_substr($string, 0, 1), 'UTF-8') . mb_substr($string, 1);
                            break;
                        case 'AF':
                            $traduction = mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
                            break;
                        case 'A':
                            $traduction = mb_convert_case($string, MB_CASE_UPPER, 'UTF-8');
                            break;
                        default:
                            $traduction = $string;
                            break;
                    }
                }
                else {
                    $traduction = str_replace('_', ' ', $matches[1][$i]);
                }
                $file = str_replace($matches[0][$i], $traduction, $file);
            }
            $this->setFile($file);
        }
    }
}
?>
