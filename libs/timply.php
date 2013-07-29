<?php
/**
 * Timply class
 *
 * Genarate webpage from html sources
 *
 * @package     Timply
 * @author      Daniel Douat <daniel.douat@aelys-info.fr>
 * @link        http://www.aelys-info.fr
 */
class timply
{
    private static $themeDir;
    private static $fileName;
    private static $dictionary;
    private $blockList;
    private $file;
    private $firstElement;
    public  $block;

    function __construct()
    {
        $this->setFile();
        $this->setBlockList();
    }


    /**
     * Load theme uri
     * @param string $uri dir of theme
     */
    public static function setUri($uri)
    {
        if (!empty($uri)) {
            $length    = strlen($url);
            $lastSlash = strpos($url, '/') + 1;
            if ($lenght !== $lastSlash) {
                $uri = $uri . '/';
            }
        }
        self::$themeDir = $uri;
    }

    /**
     * Load file of them
     * @param string $fileName filename
     */
    public static function setFileName($fileName)
    {
        if (!empty($fileName)) {
            self::$fileName = $fileName;
        }
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
        if (!empty($block)) {
            $this->setBlock($element, $data, $block);
        }
        else {
            $this->element[$element] = $data;
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
        if (is_array(self::$dictionary)) {
            $this->traduct();
        }
        $this->cleanFile();
        return $this->getFile();
    }

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
            if (file_exists(self::$themeDir . self::$fileName)) {
                $this->file = file_get_contents(self::$themeDir . self::$fileName);
            }
        }
        else {
            $this->file = $datas;
        }
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
        mb_internal_encoding('UTF-8');
        $file    = $this->getFile();
        $pattern = '/\[trad::([\d\w\-_]+):{0,2}(F|AF|A){0,1}\]/';
        preg_match_all($pattern, $file, $matches);
        $count   = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $string = self::$dictionary[$matches[1][$i]];
            if ($matches[2][$i] === 'F') {
                // Not using ucfirst to preserve locales
                $traduction = mb_strtoupper(mb_substr($string, 0, 1), 'UTF-8') . mb_substr($string, 1);
            }
            elseif ($matches[2][$i] === 'AF') {
                $traduction = mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
            }
            elseif ($matches[2][$i] === 'A') {
                $traduction = mb_convert_case($string, MB_CASE_UPPER, 'UTF-8');
            }
            else {
                $traduction = $string;
            }
            $file = str_replace($matches[0][$i], $traduction, $file);
        }
        $this->setFile($file);
    }
}
?>