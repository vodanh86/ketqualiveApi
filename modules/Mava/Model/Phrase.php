<?php

/**
 * Model for phrases
 *
 * @package Mava_Phrase
 */
class Mava_Model_Phrase extends Mava_Model
{
    /**
     * Returns the phrase specified by phrase_id
     *
     * @param integer $phraseId phrase ID
     *
     * @return array|false phrase
     */
    public function getPhraseById($phraseId)
    {
        return $this->_getDb()->fetchRow('
			SELECT *
			FROM #__phrase
			WHERE phrase_id = ?
		', $phraseId);
    }

    /**
     * Fetches a phrase from a particular language based on its title.
     * Note that if a version of the requested phrase does not exist
     * in the specified language, nothing will be returned.
     *
     * @param string $title Title
     * @param integer $languageId language ID (defaults to master language)
     *
     * @return array
     */
    public function getPhraseInLanguageByTitle($title, $languageId = 0)
    {
        return $this->_getDb()->fetchRow('
			SELECT *
			FROM #__phrase
			WHERE title = ?
				AND language_id = ?
		', array($title, $languageId));
    }

    /**
     * Gets the value for the named master phrase.
     *
     * @param string $title
     *
     * @return string Empty string if phrase is value
     */
    public function getMasterPhraseValue($title)
    {
        $phrase = $this->getPhraseInLanguageByTitle($title, 0);
        return ($phrase ? $phrase['phrase_text'] : '');
    }

    /**
     * Inserts or updates an array of master (language 0) phrases. Errors will be silently ignored.
     *
     * @param array $phrases Key-value pairs of phrases to insert/update
     * @param string $addOnId Add-on all phrases belong to
     * @param array $extra Extra fields to set
     * @param array $options
     *
     * @param array $phrases Format: [title] => value
     */
    public function insertOrUpdateMasterPhrases(array $phrases, $addOnId, array $extra = array(), array $options = array())
    {
        foreach ($phrases AS $title => $value)
        {
            $this->insertOrUpdateMasterPhrase($title, $value, $addOnId, $extra, $options);
        }
    }

    /**
     * Inserts or updates a master (language 0) phrase. Errors will be silently ignored.
     *
     * @param string $title
     * @param string $text
     * @param string $addOnId
     * @param array $extra Extra fields to set
     * @param array $options
     */
    public function insertOrUpdateMasterPhrase($title, $text, $addOnId = '', array $extra = array(), array $options = array())
    {
        $phrase = $this->getPhraseInLanguageByTitle($title, 0);

        $dw = Mava_DataWriter::create('Mava_DataWriter_Phrase', Mava_DataWriter::ERROR_SILENT);
        foreach ($options AS $key => $value)
        {
            $dw->setOption($key, $value);
        }
        if ($phrase)
        {
            $dw->setExistingData($phrase, true);
        }
        else
        {
            $dw->set('language_id', 0);
        }
        $dw->set('title', $title);
        $dw->set('phrase_text', $text);
        $dw->set('addon_id', $addOnId);
        $dw->bulkSet($extra);
        $dw->save();
    }

    /**
     * Deletes the named master phrases if they exist.
     *
     * @param array $phraseTitles Phrase titles
     * @param array $options
     */
    public function deleteMasterPhrases(array $phraseTitles, array $options = array())
    {
        foreach ($phraseTitles AS $title)
        {
            $this->deleteMasterPhrase($title, $options);
        }
    }

    /**
     * Deletes the named master phrase if it exists.
     *
     * @param string $title
     * @param array $options
     */
    public function deleteMasterPhrase($title, array $options = array())
    {
        $phrase = $this->getPhraseInLanguageByTitle($title, 0);
        if (!$phrase)
        {
            return;
        }

        $dw = Mava_DataWriter::create('Mava_DataWriter_Phrase', Mava_DataWriter::ERROR_SILENT);
        foreach ($options AS $key => $value)
        {
            $dw->setOption($key, $value);
        }
        $dw->setExistingData($phrase, true);
        $dw->delete();
    }

    /**
     * Renames a list of master phrases. If you get a conflict, it will
     * be silently ignored.
     *
     * @param array $phraseMap Format: [old name] => [new name]
     * @param array $options
     */
    public function renameMasterPhrases(array $phraseMap, array $options = array())
    {
        foreach ($phraseMap AS $oldName => $newName)
        {
            $this->renameMasterPhrase($oldName, $newName);
        }
    }

    /**
     * Renames a master phrase. If you get a conflict, it will
     * be silently ignored.
     *
     * @param string $oldName
     * @param string $newName
     * @param array $options
     */
    public function renameMasterPhrase($oldName, $newName, array $options = array())
    {
        $phrase = $this->getPhraseInLanguageByTitle($oldName, 0);
        if (!$phrase)
        {
            return;
        }

        $dw = Mava_DataWriter::create('Mava_DataWriter_Phrase', Mava_DataWriter::ERROR_SILENT);
        foreach ($options AS $key => $value)
        {
            $dw->setOption($key, $value);
        }
        $dw->setExistingData($phrase, true);
        $dw->set('title', $newName);
        $dw->save();
    }


    /**
     * Determines if the visiting user can modify a phrase in the specified language.
     * If debug mode is not enabled, users can't modify phrases in the master language.
     *
     * @param integer $languageId
     *
     * @return boolean
     */
    public function canModifyPhraseInLanguage($languageId)
    {
        return ($languageId != 0 || Mava_Application::debugMode());
    }

    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel()
    {
        return $this->getModelFromCache('Mava_Model_Language');
    }
}