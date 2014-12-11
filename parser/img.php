<?php
class imgParser
extends parser
implements parserTemplate
{
    private static $type;
    private static $contentTypes;
    private static $themeIsOk;

    function __construct()
    {
        $this->uri = $GLOBALS['config']['themeDir'] . 'fonts/';
        $this->fileName = 'image.' . self::$type;
        $this->width    = 1024;
        $this->fontRGB  = array(85, 85, 85);
        $this->bgRGB  = array(255, 255, 255);
        $this->fontName = 'texgyrepagella-italic.ttf';
        $this->setTheme();
    }

    function parse($elements)
    {
        if (function_exists(imagecreatefrom . self::$type)) {
            // factor for width = 1024px, in comment value for 1024px
            $height               = round($this->width * 0.5625); // 576
            $margin               = round($this->width * 0.0390625); // 40
            $authorFontSize       = round($this->width * 0.024414063); // 25
            $permalinkBlockHeight = round($this->width * 0.048828125); // 50
            $quoteFontSize        = round($this->width * 0.034179688); // 35
            $quoteLineHeight      = round($this->width * 0.048828125); // 50
            $permalinkFontSize    = round($this->width * 0.01953125); // 20
            $quote                = wordwrap(trim($elements[0]->getText()), 50, PHP_EOL);

            // Get quote block size
            $quoteBox    = imageftbbox($quoteFontSize, 0, $this->font, $quote);
            $quoteHeight = abs($quoteBox[5] - $quoteBox[1]); // distance from top to bottom

            // Get author box size
            $authorBox    = imageftbbox($authorFontSize, 0, $this->font, '(' . $elements[0]->getAuthor() . ')');
            $authorWidth  = abs($authorBox[4]) - abs($authorBox[0]); // distance from left to right
            $authorHeight = abs($authorBox[5]) - abs($authorBox[1]);

            // Get permalink box size
            $permalinkBox    = imageftbbox($permalinkFontSize, 0,  $this->font, rtrim($this->returnSiteBase(), '/') . $this->returnPermalink($elements[0]->getPermalink()));
            $permalinkWidth  = abs($permalinkBox[4]) - abs($permalinkBox[0]);
            $permalinkHeight = abs($permalinkBox[5]) - abs($permalinkBox[1]);
            
            $blockHeight     = $quoteHeight + $authorHeight + $margin;
            $maxBlockHeight = $height - $permalinkHeight - ($margin * 4);
            //Keep quote vertical align : middle
            $y = ($height - $permalinkHeight) / 2 - $blockHeight / 2;
            // If quote is higher than block height, changing picture height, $y start a margin top
            if ($blockHeight > $maxBlockHeight) {
                $height = ($blockHeight - $maxBlockHeight) + $height;
                $y = $margin + round($quoteFontSize / 2); // A cause de la ligne de base de la police pour imagettftext
            }

            // Create image
            if (file_exists($this->uri . $this->fileName)) {
                $content = imagecreatefrom . self::$type;
            }
            else {
                $content = imagecreate($this->width, $height);
            }
            $backgroundColor = imagecolorallocate($content, $this->bgRed, $this->bgGreen, $this->bgBlue);
            $fontColor       = imagecolorallocate($content, $this->fontRed, $this->fontGreen, $this->fontBlue);
            $nextLine        = $quoteFontSize + abs($quoteLineHeight - $quoteFontSize);

            // Adding quote
            imagettftext($content, $quoteFontSize, 0, $margin, $y, $fontColor, $this->font, $quote);

            // Adding author
            imagettftext($content, $authorFontSize, 0, $margin, $y + $quoteHeight + $margin, $fontColor, $this->font, '(' . $elements[0]->getAuthor() . ')');

            // Adding permalink
            imagettftext($content, $permalinkFontSize, 0, imagesx($content) - $permalinkWidth - $margin, imagesy($content) - $margin,
                         $fontColor, $this->font, rtrim($this->returnSiteBase(), '/') . $this->returnPermalink($elements[0]->getPermalink()));

            // Render
            $functionName = 'image' . self::$type;
            $functionName($content);
            imagedestroy($content);
        }
        else {
            exit('problem with image generation');
        }
    }

    public function loadHeader($elements = '')
    {
        self::setType();
        $contentType = self::returnContentType();
        // Control if we can create image
        if ($contentType) {
            header('Content-type: ' . $contentType);
        }
        else {
            exit('Bad content type');
        }
    }

    private static function setType()
    {
        if (!empty($_GET['t'])) {
            self::$type = $_GET['t'];
        }
        else {
            self::$type = 'png';
        }
    }
    private function setTheme()
    {
        $fontRGB = $this->fontRGB;
        $bgRGB   = $this->bgRGB;
        if (!empty($_GET['t'])) {
            self::$type = $_GET['t'];
        }
        if (!empty($_GET['wi'])) {
            $this->width = (int) $_GET['wi'];
        }
        if (!empty($_GET['fc'])) {
            $fontRGB = explode(',', $_GET['fc']);
        }
        if (!empty($_GET['bgc'])) {
            $bgRGB   = explode(',', $_GET['bgc']);
        }
        if (count($fontRGB) == 3 && count($bgRGB) == 3) {
            list($this->fontRed, $this->fontGreen, $this->fontBlue) = $fontRGB;
            list($this->bgRed, $this->bgGreen, $this->bgBlue)       = $bgRGB;
        }
        else {
            exit('bad colors');
        }
        if (file_exists($this->uri . $this->fontName)) {
            $this->font = $this->uri . $this->fontName;
        }
        else {
            exit('font file not exist');
        }
    }

    private function gdSupport()
    {
        $gd = gd_info();
        $type = strtoupper(self::$type);
        if ($type === 'FREETYPE') {
            $key = 'FreeType Support';
        }
        elseif ($type === 'GIF') {
            $key = 'GIF Create Support';
        }
        else {
            $key = $type . ' Support';
        }
        return $gd[$key];
    }

    private static function returnContentType()
    {
        if (defined(IMAGETYPE_ . strtoupper(self::$type))) {
            if (image_type_to_mime_type(constant(IMAGETYPE_ . strtoupper(self::$type)))) {
                $result = image_type_to_mime_type(constant(IMAGETYPE_ . strtoupper(self::$type)));
            }
            else {
                $result = FALSE;
            }
        }
        else {
            $result = FALSE;
        }
        return $result;
    }
}
?>