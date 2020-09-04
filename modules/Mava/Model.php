<?php

abstract class Mava_Model
{
   protected $_cache = null;

    protected $_db = null;

    protected $_allowCachedRead = true;

    protected $_localCacheData = array();

    protected $_modelCache = array();

    public function __construct()
    {
    }

    public function setLocalCacheData($name, $value)
    {
        $this->_localCacheData[$name] = $value;
    }

    public function resetLocalCacheData($name = null)
    {
        if ($name === null)
        {
            $this->_localCacheData = array();
        }
        else if (isset($this->_localCacheData[$name]))
        {
            unset($this->_localCacheData[$name]);
        }
    }

    protected function _getLocalCacheData($name)
    {
        return isset($this->_localCacheData[$name]) ? $this->_localCacheData[$name] : false;
    }

    /**
     * @param $class
     * @return mixed
     * @throws Mava_Exception
     */
    public function getModelFromCache($class)
    {
        if (!isset($this->_modelCache[$class]))
        {
            $this->_modelCache[$class] = Mava_Model::create($class);
        }

        return $this->_modelCache[$class];
    }

    protected function _getCache($forceCachedRead = false)
    {
        if (!$this->_allowCachedRead && !$forceCachedRead)
        {
            return false;
        }

        if ($this->_cache === null)
        {
            $this->_cache = Mava_Application::getCache();
        }

        return $this->_cache;
    }

    /**
     * @return Mava_Database
     */
    protected function _getDb()
    {
        if ($this->_db === null)
        {
            $this->_db = Mava_Application::getDb();
        }

        return $this->_db;
    }

    public function setAllowCachedRead($allowCachedRead)
    {
        $this->_allowCachedRead = (bool)$allowCachedRead;
    }

    public static function create($class)
    {
        $createClass = Mava_Application::resolveDynamicClass($class, 'model');
        if (!$createClass)
        {
            throw new Mava_Exception("Invalid model '$class' specified");
        }

        return new $createClass;
    }
}