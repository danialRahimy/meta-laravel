<?php

/**
 * @author Danial Rahimy
 * @package danialrahimy/meta-laravel
 * @license MIT
 * @since 2020-11-06 V1.0.1
 * @version 1.0.0
 */
namespace Danialrahimy\MetaLaravel;

class Meta
{
    protected static $configPath = '/resources/etc/sourcesHtml.json';
    protected static $productionPrefix = '';
    protected static $productionVersion = '1';

    /**
     * @param string $prefix
     */
    public static function setProductionPrefix(string $prefix)
    {
        self::$productionPrefix = $prefix;
    }

    /**
     * @param string $version
     */
    public static function setProductionVersion(string $version)
    {
        self::$productionVersion = $version;
    }

    /**
     * @param string $configPath
     */
    public static function setConfigPath(string $configPath)
    {
        self::$configPath = $configPath;
    }

    /**
     * @return array
     * @throws MetaException
     */
    protected static function getMetaConfig() : array
    {
        $path = base_path() . self::$configPath;

        if (!file_exists($path))
            throw new MetaException('config file not found in ' . $path);

        $data = file_get_contents($path);
        $data = json_decode($data, true);

        if (isset($data['minify']))
            $data = $data['minify'];

        return $data;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     * @throws MetaException
     */
    public static function get(string $type, string $id) : string
    {
        $css = self::getCss($type, $id);
        $js = self::getJs($type, $id);

        return $css . $js;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     * @throws MetaException
     */
    public static function getCss(string $type, string $id)
    {
        $data = self::getMetaConfig();
        $css = '';
        $env = config('app.env', 'production');

        if (isset($data[$type][$id]['css'])){

            if ($env === 'local')
                $css = self::getCssMetaDev($data, $type, $id);
            else
                $css = self::getCssMetaProd($type, $id);
        }

        return $css;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     * @throws MetaException
     */
    public static function getJs(string $type, string $id)
    {
        $data = self::getMetaConfig();
        $js = '';
        $env = config('app.env', 'production');

        if (isset($data[$type][$id]['js'])){

            if ($env === 'local')
                $js = self::getJsMetaDev($data, $type, $id);
            else
                $js = self::getJsMetaProd($type, $id);
        }

        return $js;
    }

    /**
     * @param array $data
     * @param string $type
     * @param string $id
     * @return string
     */
    protected static function getCssMetaDev(array $data, string $type, string $id) : string
    {
        $css = '';

        foreach ($data[$type]['general']['css'] as $cssPath){

            $css .= "<link href='/{$cssPath}' rel='stylesheet'>" . PHP_EOL;
        }

        foreach ($data[$type][$id]['css'] as $cssPath){

            $css .= "<link href='/{$cssPath}' rel='stylesheet'>" . PHP_EOL;
        }

        return $css;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     */
    protected static function getCssMetaProd(string $type, string $id) : string
    {
        return '<link href=\'' . self::$productionPrefix . "/css/{$type}/{$id}.css?version=" . self::$productionVersion . '\' rel=\'stylesheet\'>' . PHP_EOL;
    }

    /**
     * @param array $data
     * @param string $type
     * @param string $id
     * @return string
     */
    protected static function getJsMetaDev(array $data, string $type, string $id) : string
    {
        $js = '';

        foreach ($data[$type]['general']['js'] as $cssPath){

            $js .= "<script src='/{$cssPath}' rel='script'></script>" . PHP_EOL;
        }

        foreach ($data[$type][$id]['js'] as $cssPath){

            $js .= "<script src='/{$cssPath}' rel='script'></script>" . PHP_EOL;
        }

        return $js;
    }

    /**
     * @param string $type
     * @param string $id
     * @return string
     */
    protected static function getJsMetaProd(string $type, string $id) : string
    {
        return '<script defer src=\'' . self::$productionPrefix . "/js/{$type}/{$id}.js?version=" . self::$productionVersion . '\' rel=\'script\'></script>' . PHP_EOL;
    }
}
