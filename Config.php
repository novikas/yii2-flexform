<?php

/**
 * @package   yii2-krajee-base
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015
 * @version   1.7.4
 */

namespace novik\flexform;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Global configuration helper class  
 *
 */
class Config
{
    const VENDOR_NAME = "novik-a/";
    const NAMESPACE_PREFIX = "\\novik\\";
    const DEFAULT_REASON = "for your selected functionality";

    /**
     * Get the current directory of the extended class object
     *
     * @param mixed $object the called object instance
     *
     * @return string
     */
    public static function getCurrentDir($object)
    {
        if (empty($object)) {
            return '';
        }
        $child = new \ReflectionClass($object);
        return dirname($child->getFileName());
    }

    /**
     * Check if a file exists
     *
     * @param string $file the file with path in URL format
     *
     * @return bool
     */
    public static function fileExists($file)
    {
        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        return file_exists($file);
    }

    /**
     * Gets the module
     *
     * @param string $m the module name
     *
     * @return Module
     */
    public static function getModule($m)
    {
        $mod = Yii::$app->controller->module;
        return $mod && $mod->getModule($m) ? $mod->getModule($m) : Yii::$app->getModule($m);
    }

    /**
     * Initializes and validates the module
     *
     * @param string $class the Module class name
     *
     * @return \yii\base\Module
     *
     * @throws InvalidConfigException
     */
    public static function initModule($class)
    {
        $m = $class::MODULE;
        $module = $m ? static::getModule($m) : null;
        if ($module === null || !$module instanceof $class) {
            throw new InvalidConfigException("The '{$m}' module MUST be setup in your Yii configuration file and must be an instance of '{$class}'.");
        }
        return $module;
    }
}
