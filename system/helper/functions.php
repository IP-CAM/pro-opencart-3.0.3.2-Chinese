<?php
if (!function_exists('registry')) {
    /**
     * Get Registry Instance
     *
     * @param null $type
     * @return Registry|Config|Session|Document|Url|Loader|\Cart\Currency|Language|Request|Log|\Cart\Customer
     * @throws Exception
     */
    function registry($type = null)
    {
        if ($type) {
            return Registry::getSingleton()->get($type);
        }

        return Registry::getSingleton();
    }
}

if (!function_exists('session')) {
    /**
     * Get Session Instance
     *
     * @return Session
     * @throws Exception
     */
    function session()
    {
        return registry('session');
    }
}

if (!function_exists('current_language_code')) {
    /**
     * Get current language code
     *
     * @return string
     * @throws Exception
     */
    function current_language_code()
    {
        $session_data = session()->data;
        return strtolower($session_data['language']);
    }
}

if (!function_exists('is_zh_cn')) {
    /**
     * Check if the language is zh_cn
     *
     * @throws Exception
     */
    function is_zh_cn()
    {
        return current_language_code() == 'zh-cn';
    }
}