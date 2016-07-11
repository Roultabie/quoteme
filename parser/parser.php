<?php
abstract class parser
{
    public static $cacheState;
    public static $cacheDir;

    function __construct()
    {
        
    }

    final protected function isUTF8($text)
    {
        #
    }

    final protected function toUTF8($text)
    {
        #
    }

    public static function clearCache()
    {
        if (is_dir(self::$cacheDir) && is_writable(self::$cacheDir)) {
            $handle = opendir(self::$cacheDir);
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..') {
                    unlink(self::$cacheDir . '/' . $file);
                }
            }
            closedir($handle);
        }
    }

    public static function startCache()
    {
        if (self::$cacheState) {
            $file = self::$cacheDir . '/' . hash('sha1', serialize($_GET));
            if (file_exists($file)) {
                readfile($file);
                exit();
            }
            else {
                ob_start();
            }
        }
    }

    public static function endCache()
    {
        if (self::$cacheState) {
            if (is_dir(self::$cacheDir) && is_writable(self::$cacheDir)) {
                $file = self::$cacheDir . '/' . hash('sha1', serialize($_GET));
                if (!file_exists($file)) {
                    file_put_contents($file, ob_get_contents());
                }
            }
            ob_flush();
            ob_end_clean();
        }
    }
}
?>