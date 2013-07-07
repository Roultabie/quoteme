<?php
class pngParser
extends parser
implements parserTemplate
{
    function __construct()
    {
        $this->uri      = $GLOBALS['config']['themeDir'] . '/';
        $this->font     = 'fonts/texgyrepagella-italic.ttf';
        $this->fileName = 'quote.png';
        $this->width    = 1024;
        $this->height   = 576;
    }
    public function parse($elements)
    {
        $x = 10;
        $y = 10;


        header("Content-type: image/png");
        //$string = $elements[0]->getText();
        if (file_exists($this->uri . $this->fileName)) {
            $im = imagecreatefrompng($this-> uri . $this->fileName);
        }
        else {
            $im = imagecreate($this->width, $this->height);
        }
        $background = imagecolorallocate($im, 255, 255, 255);
        $black      = imagecolorallocate($im, 0, 0, 0);
        $grey       = imagecolorallocate($im, 85, 85, 85);

        $font_size = 35;
        $font_color =  $grey;

        // Text to be placed as a paragraph
        $text = '« ' . $elements[0]->getText() . ' »';

        // Break it up into pieces 125 characters long
        $lines = explode('|', wordwrap($text, 50, '|'));
        $nbLines = count($lines);
        $baseY = 50;
        $margin = 30;
        $authorHeight = 25;
        $permalinkBlockHeight = 50;
        $permalinkY = $permalinkBlockHeight - 30;
        // (height - (margin-top + margin-bottom) - (emptyLine + authorHeight) - space betwin athor and permalink) - permalink) / fontSize
        $txtHeight = $nbLines * $baseY + $baseY + $authorHeight;
        $txtBlockHeight = $this->height - $permalinkBlockHeight;
        $y = $txtBlockHeight / 2 - $txtHeight / 2;

        // Starting Y position
        //$y = 80;

        // Loop through the lines and place them on the image
        foreach ($lines as $line)
        {
            imagettftext($im, $font_size, 0, 40, $y, $grey, $this->uri . $this->font, $line);

            // Increment Y so the next line is below the previous line
            $y += 40;
        }

        $y = $y + 40;
        imagettftext($im, $authorHeight, 0, 40, $y, $grey, $this->uri . $this->font, '(' . $elements[0]->getAuthor() . ')');
        //imagettftext($im, 20, 0, 790, 555, $black, $this->uri . $this->font, $this->returnPermalink($elements[0]->getPermalink()));
        //imagestring($im, 1, $x, $y, $string, $black);
        imagettftext($im, $permalinkY, 0, 768, 535, $grey, $this->uri . $this->font,  $this->returnPermalink($elements[0]->getPermalink()));
        imagepng($im);
        imagedestroy($im);
    }
}
?>