<?php

/**
 * Autoloader 
 */

namespace Kant\Bootstrap;

class KantAutoloaderInit {

    protected static $_autoCoreClass = array(
        'Kant\KantDispatch' => 'Foundation/KantDispatch',
        'Kant\KantRegistry' => 'Foundation/KantRegistry',
        'Kant\KantException' => 'Foundation/KantException'
    );

    /**
     * Get Loader
     */
    public static function getLoader() {
        spl_autoload_register(array('self', 'autoload'), true, true);
    }

    /**
     * Load core class
     * 
     * @param type $className
     * @param type $dir
     * @return boolean
     */
    public static function autoload($className, $dir = '') {
        if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }
        try {
            if (in_array($className, array_keys(self::$_autoCoreClass)) == true) {
                $filename = KANT_PATH . self::$_autoCoreClass[$className] . ".php";
            } else {
                if (strpos($className, "\\") !== false) {
                    if (strpos($className, "Kant") === 0) {
                        $className = str_replace('Kant\\', '', $className);
                        $className = str_replace('\\', '/', $className) . ".php";
                        $filename = KANT_PATH . $className;
                    } else if (strpos($className, "Bootstrap") !== false) {
                        $className = str_replace('\\', '/', $className) . ".php";
                        $filename = APP_PATH . $className;
                    } else if (strpos($className, "Model") !== false || strpos($className, "Controller") !== false) {
                        $className = str_replace('\\', '/', $className) . ".php";
                        $filename = MODULE_PATH . $className;
                    } else {
                        $className = str_replace('\\', '/', $className) . ".php";
                        $filename = APP_PATH . $className;
                    }
                } else {
                    $filename = $className;
                }
            }
            self::inclde($filename);
        } catch (RuntimeException $e) {
            exit('Require File Error: ' . $e->getMessage());
        }
        return true;
    }

    /**
     * Include file
     * 
     * @staticvar array $_importFiles
     * @param type $filename
     * @return boolean
     */
    public static function inclde($filename) {
        static $files = array();
        if (!isset($files[$filename])) {
            if (file_exists($filename)) {
                require $filename;
                $files[$filename] = true;
            } else {
                $files[$filename] = false;
            }
        }
        return $files[$filename];
    }

    /**
     * Register autoload function
     *
     * @param string $func
     * @param boolean $enable
     */
    public static function registerAutoload($func = 'self::autoload', $enable = true) {
        $enable ? spl_autoload_register($func) : spl_autoload_unregister($func);
    }

}

KantAutoloaderInit::getLoader();
