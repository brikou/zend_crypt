<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Translator;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Translator\Exception
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translator
{

    /**
     * Adapter names constants
     */
    const AN_ARRAY   = 'ArrayAdapter';
    const AN_CSV     = 'Csv';
    const AN_GETTEXT = 'Gettext';
    const AN_INI     = 'Ini';
    const AN_QT      = 'Qt';
    const AN_TBX     = 'Tbx';
    const AN_TMX     = 'Tmx';
    const AN_XLIFF   = 'Xliff';
    const AN_XMLTM   = 'XmlTm';

    const LOCALE_DIRECTORY = 'directory';
    const LOCALE_FILENAME  = 'filename';

    /**
     * Adapter
     *
     * @var \Zend\Translator\Adapter\Adapter
     */
    private $_adapter;
    private static $_cache = null;

    /**
     * Generates the standard translation object
     *
     * @param  string              $adapter  Adapter to use
     * @param  array               $data     OPTIONAL Translation source data for the adapter
     *                                       Depends on the Adapter
     * @param  string|\Zend\Locale\Locale  $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL options for the adapter
     * @throws \Zend\Translator\Exception
     */
    public function __construct($adapter, $data = null, $locale = null, array $options = array())
    {
        $this->setAdapter($adapter, $data, $locale, $options);
    }

    /**
     * Sets a new adapter
     *
     * @param  string              $adapter  Adapter to use
     * @param  string|array        $data     OPTIONAL Translation data
     * @param  string|\Zend\Locale\Locale  $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL Options to use
     * @throws \Zend\Translator\Exception
     */
    public function setAdapter($adapter, $data = null, $locale = null, array $options = array())
    {
        $adapter = 'Zend\\Translator\\Adapter\\' . ucfirst($adapter);

        if (!class_exists($adapter, true)) {
            throw new Exception("Adapter " . $adapter . " does not exist and cannot be loaded.");
        }

        if (self::$_cache !== null) {
            call_user_func(array($adapter, 'setCache'), self::$_cache);
        }

        $this->_adapter = new $adapter($data, $locale, $options);
        if (!$this->_adapter instanceof Adapter\Adapter) {
            throw new Exception("Adapter " . $adapter . " does not extend Zend_Translate_Adapter");
        }
    }

    /**
     * Returns the adapters name and it's options
     *
     * @return \Zend\Translator\Adapter\Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the set cache
     *
     * @return \Zend\Cache\Frontend\Core The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Sets a cache for all instances of Zend_Translate
     *
     * @param  \Zend\Cache\Frontend\Core $cache Cache to store to
     * @return void
     */
    public static function setCache(\Zend\Cache\Frontend\Core $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache !== null) {
            return true;
        }

        return false;
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache()
    {
        self::$_cache->clean();
    }

    /**
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
        if (method_exists($this->_adapter, $method)) {
            return call_user_func_array(array($this->_adapter, $method), $options);
        }
        throw new Exception("Unknown method '" . $method . "' called!");
    }
}