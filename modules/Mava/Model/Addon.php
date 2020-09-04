<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/11/14
 * Time: 10:23 AM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Model_Addon extends Mava_Model {
    /**
     * @return array
     */
    public function getAllAddon(){
        $addon = $this->_getDb()->query("SELECT * FROM #__addon ORDER BY CONVERT(title USING utf8)");
        return $addon->rows;
    }

    /**
     * @return array
     */
    public function getAllActiveAddon(){
        $addon = $this->_getDb()->query("SELECT * FROM #__addon WHERE `active`=1 ORDER BY CONVERT(title USING utf8)");
        return $addon->rows;
    }

    /**
     * Gets the specified add-on if it exists.
     *
     * @param string $addOnId
     *
     * @return array|false
     */
    public function getAddOnById($addOnId)
    {
        return $this->_getDb()->fetchRow('
			SELECT *
			FROM #__addon
			WHERE addon_id = "'. $addOnId .'"
		');
    }

    /**
     * Gets the version ID/string for the specified add-on.
     *
     * @param string $addOnId
     *
     * @return array|false
     */
    public function getAddOnVersion($addOnId)
    {
        if ($addOnId === '')
        {
            return false;
        }
        else if ($addOnId === 'Mava')
        {
            return array(
                'version_id' => Mava_Application::$versionId,
                'version_string' => Mava_Application::$version
            );
        }
        else
        {
            return $this->_getDb()->fetchRow('
				SELECT version_id, version_string
				FROM xf_addon
				WHERE addon_id = ?
			', $addOnId);
        }
    }
}