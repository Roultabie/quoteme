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
    private $themeDir;
    private $blockList;
    private $file;
    private $firstElement;
    public $block;
    
    function __construct($source)
    {
        $this->themeDir = (TIMPLY_DIR) ? TIMPLY_DIR : 'themes/default/';
        $this->fileName = $source;
        $this->setFile();
        $this->setBlockList();
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
        return $this->getFile();
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
            $file = $this->getFile();
            foreach ($this->element as $key => $data) {
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
            $this->file = file_get_contents($this->themeDir . $this->fileName);
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
}
?>