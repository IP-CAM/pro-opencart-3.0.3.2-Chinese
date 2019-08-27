<?php
namespace Utils;

class Helper
{
    private static $helper;

    public static function getSingleton()
    {
        if (self::$helper instanceOf Helper) {
            return self::$helper;
        } else {
            return self::$helper = new Helper;
        }
    }
    public function __call($name, $arguments)
    {
        if (!function_exists($name)) {
            throw new \Exception("The function {$name} is not exist!");
        }
        return call_user_func($name, $arguments);
    }

    public function template($route)
    {
        if (config('config_theme') == 'default') {
            if (is_file(DIR_TEMPLATE . config('theme_default_directory') . "/template/{$route}.twig")) {
                return config('theme_default_directory') . "/template/{$route}.twig";
            }
        }

        if (is_file(DIR_TEMPLATE . config('config_theme') . "/template/{$route}.twig")) {
            return config('config_theme') . "/template/{$route}.twig";
        }

        return "default/template/{$route}.twig";
    }
}
