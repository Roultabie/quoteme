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
            $x                    = round($this->width * 0.0390625); // 40
            $y                    = round($this->width * 0.048828125); // 50
            $margin               = round($this->width * 0.029296875); // 30
            $authorFontSize       = round($this->width * 0.024414063); // 25
            $permalinkBlockHeight = round($this->width * 0.048828125); // 50
            $quoteFontSize        = round($this->width * 0.034179688); // 35
            $nextLine             = round($this->width * 0.0390625); // 40
            $permalinkFontSize    = round($this->width * 0.01953125); // 20
            $permalinkX           = round($this->width * 0.0390625); // 40 margin right
            $permalinkY           = round($this->width * 0.522460938); // 535

            if (file_exists($this->uri . $this->fileName)) {
                $content = imagecreatefrom . self::$type;
            }
            else {
                $content = imagecreate($this->width, $height);
            }
            $backgroundColor = imagecolorallocate($content, $this->bgRed, $this->bgGreen, $this->bgBlue);
            $fontColor       = imagecolorallocate($content, $this->fontRed, $this->fontGreen, $this->fontBlue);
            // Text to be placed as a paragraph
            $text                  = '« ' . $elements[0]->getText() . ' »';
            // Break it up into pieces 125 characters long
            $lines                 = explode('|', wordwrap($text, 50, '|'));
            $nbLines               = count($lines);

            // Keep quote vertical align : middle
            $txtHeight             = $nbLines * $y + $y + $authorFontSize;
            $txtBlockHeight        = $height - $permalinkBlockHeight;
            $y                     = $txtBlockHeight / 2 - $txtHeight / 2;
            foreach ($lines as $line) {
                imagettftext($content, $quoteFontSize, 0, $x, $y, $fontColor, $this->font, $line);
                $y += $nextLine; // Increment Y so the next line is below the previous line
            }
            $y = $y + $nextLine;
            imagettftext($content, $authorFontSize, 0, $x, $y, $fontColor, $this->font, '(' . $elements[0]->getAuthor() . ')');
            $permalinkBox    = imageftbbox($permalinkFontSize, 0,  $this->font, $this->returnPermalink($elements[0]->getPermalink()));
            $permalinkWidth  = abs($permalinkBox[0]) + abs($permalinkBox[2]); // distance from left to right
            $permalinkHeight = abs($permalinkBox[1]) + abs($permalinkBox[5]); // distance from top to bottom
            $permalinkXStart = $this->width - $permalinkWidth - $permalinkX;
            imagettftext($content, $permalinkFontSize, 0, $permalinkXStart, $permalinkY, $fontColor, $this->font,  $this->returnPermalink($elements[0]->getPermalink()));
            $functionName = 'image' . self::$type;
            $functionName($content);
            imagedestroy($content);
        }
        else {
            exit('problem with image generation');
        }
    }

    public static function loadHeader()
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