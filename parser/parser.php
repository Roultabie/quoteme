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

    final protected function returnUri()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    }

    final protected function returnScriptUri()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    }

    final protected function returnPermalink($permalink)
    {
        return $_SERVER['HTTP_HOST'] . '/?' . $permalink;
    }

    private static function clearCache()
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

    public static function addCache()
    {
        if (self::$cacheState) {
            if (is_dir(self::$cacheDir) && is_writable(self::$cacheDir)) {
                $file = self::$cacheDir . '/' . hash('sha1', serialize($_GET));
                if (!file_exists($file)) {
                    file_put_contents($file, ob_get_contents());
                }
            }
        }
    }
    public static function loadCache()
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
}
?>