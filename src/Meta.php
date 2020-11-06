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
    const configPath = "/resources/etc/sourcesHtml.json";

    /**
     * @return array
     * @throws MetaException
     */
    protected static function getMetaConfig() : array
    {
        $path = base_path() . self::configPath;

        if (!file_exists($path))
            throw new MetaException("config file not found in " . $path);

        $data = file_get_contents($path);
        $data = json_decode($data, true);

        if (isset($data["minify"]))
            $data = $data["minify"];

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
        $css = "";
        $version = env("VERSION", "PROD");

        if (isset($data[$type][$id]["css"])){

            if ($version === "dev")
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
        $js = "";
        $version = env("VERSION", "PROD");

        if (isset($data[$type][$id]["js"])){

            if ($version === "dev")
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
        $css = "";

        foreach ($data[$type][$id]["css"] as $cssPath){

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
        return "<link href='/css/{$type}/{$id}.css' rel='stylesheet'>" . PHP_EOL;
    }

    /**
     * @param array $data
     * @param string $type
     * @param string $id
     * @return string
     */
    protected static function getJsMetaDev(array $data, string $type, string $id) : string
    {
        $js = "";

        foreach ($data[$type][$id]["js"] as $cssPath){

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
        return "<script src='/js/{$type}/{$id}.js' rel='script'></script>" . PHP_EOL;
    }
}
