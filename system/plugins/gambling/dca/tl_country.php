<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Table tl_country
 */
$GLOBALS['TL_DCA']['tl_country'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
                'country' => 'unique',
			)
		)
	),

	// List
	'list' => array
	(
		'label' => array
		(
			'fields'                  => array('country'),
			'callback'                => array('tl_country', 'listCallback')
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'country, name, short_name'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
        'country' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_country']['country'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => ['tl_country', 'getCountriesAsOptions'],
            'required'                => true,
            'eval'                    => array('mandatory'=>true, 'unique'=>true),
            'sql'                     => "varchar(5) NOT NULL"
        ),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_country']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'short_name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_country']['short_name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
            'required'                => true,
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		)
	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
use Contao\Backend;
use Contao\Image;
use Contao\Input;

class tl_country extends \Contao\Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\\BackendUser', 'User');
	}


	public function listCallback($item)
	{
		$item['fields'][0] = \Contao\System::getCountriesWithFlags()[$item['fields'][0]];
		return $item;
	}

	/**
	 * Return a checkbox to delete session data
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function sessionField(DataContainer $dc)
	{
		if (Input::post('FORM_SUBMIT') == 'tl_country')
		{
			$arrPurge = Input::post('purge');

			if (is_array($arrPurge))
			{
				$this->import('Contao\\Automator', 'Automator');

				if (in_array('purge_session', $arrPurge))
				{
					$this->Session->setData(array());
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_country']['sessionPurged']);
				}

				if (in_array('purge_images', $arrPurge))
				{
					$this->Automator->purgeImageCache();
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_country']['htmlPurged']);
				}

				if (in_array('purge_pages', $arrPurge))
				{
					$this->Automator->purgePageCache();
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_country']['tempPurged']);
				}
			}
		}

		return '
<div>
  <fieldset class="tl_checkbox_container">
    <legend>'.$GLOBALS['TL_LANG']['tl_country']['session'][0].'</legend>
    <input type="checkbox" id="check_all_purge" class="tl_checkbox" onclick="Backend.toggleCheckboxGroup(this, \'ctrl_purge\')"> <label for="check_all_purge" style="color:#a6a6a6"><em>'.$GLOBALS['TL_LANG']['MSC']['selectAll'].'</em></label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_0" class="tl_checkbox" value="purge_session" onfocus=""> <label for="opt_purge_0">'.$GLOBALS['TL_LANG']['tl_country']['sessionLabel'].'</label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_1" class="tl_checkbox" value="purge_images" onfocus=""> <label for="opt_purge_1">'.$GLOBALS['TL_LANG']['tl_country']['htmlLabel'].'</label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_2" class="tl_checkbox" value="purge_pages" onfocus=""> <label for="opt_purge_2">'.$GLOBALS['TL_LANG']['tl_country']['tempLabel'].'</label>
  </fieldset>'.$dc->help().'
</div>';
	}


	/**
	 * Return all modules except profile modules
	 *
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();

		foreach ($GLOBALS['BE_MOD'] as $k=>$v)
		{
			if (!empty($v))
			{
				unset($v['undo']);
				$arrModules[$k] = array_keys($v);
			}
		}

		return $arrModules;
	}


	/**
	 * Prevent administrators from downgrading their own account
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function checkAdminStatus($varValue, DataContainer $dc)
	{
		if ($varValue == '' && $this->User->id == $dc->id)
		{
			$varValue = 1;
		}

		return $varValue;
	}


	/**
	 * Prevent administrators from disabling their own account
	 *
	 * @param mixed         $varValue
	 * @param DataContainer $dc
	 *
	 * @return mixed
	 */
	public function checkAdminDisable($varValue, DataContainer $dc)
	{
		if ($varValue == 1 && $this->User->id == $dc->id)
		{
			$varValue = '';
		}

		return $varValue;
	}


	/**
	 * Store the date when the account has been added
	 *
	 * @param DataContainer $dc
	 */
	public function storeDateAdded(DataContainer $dc)
	{
		// Return if there is no active record (override all)
		if (!$dc->activeRecord || $dc->activeRecord->dateAdded > 0)
		{
			return;
		}

		// Fallback solution for existing accounts
		if ($dc->activeRecord->lastLogin > 0)
		{
			$time = $dc->activeRecord->lastLogin;
		}
		else
		{
			$time = time();
		}

		$this->Database->prepare("UPDATE tl_country SET dateAdded=? WHERE id=?")
					   ->execute($time, $dc->id);
	}


	/**
	 * Check whether the user session should be removed
	 *
	 * @param DataContainer $dc
	 */
	public function checkRemoveSession(DataContainer $dc)
	{
		if ($dc->activeRecord)
		{
			if ($dc->activeRecord->disable || ($dc->activeRecord->start != '' && $dc->activeRecord->start > time()) || ($dc->activeRecord->stop != '' && $dc->activeRecord->stop < time()))
			{
				$this->removeSession($dc);
			}
		}
	}


	/**
	 * Remove the session if a user is deleted (see #5353)
	 *
	 * @param DataContainer $dc
	 */
	public function removeSession(DataContainer $dc)
	{
		if (!$dc->activeRecord)
		{
			return;
		}

		if ($dc->activeRecord->disable || Input::get('act') == 'delete' || Input::get('act') == 'deleteAll')
		{
			$this->Database->prepare("DELETE FROM tl_session WHERE name='BE_USER_AUTH' AND pid=?")
						   ->execute($dc->activeRecord->id);
		}
	}


	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_country::disable', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.$row['disable'];

		if ($row['disable'])
		{
			$icon = 'invisible.gif';
		}

		// Protect admin accounts
		if (!$this->User->isAdmin && $row['admin'])
		{
			return Image::getHtml($icon) . ' ';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['disable'] ? 0 : 1) . '"').'</a> ';
	}


	/**
	 * Disable/enable a user group
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		// Set the ID and action
		Input::setGet('id', $intId);
		Input::setGet('act', 'toggle');

		if ($dc)
		{
			$dc->id = $intId; // see #8043
		}

		$this->checkPermission();

		// Protect own account
		if ($this->User->id == $intId)
		{
			return;
		}

		// Check the field access
		if (!$this->User->hasAccess('tl_country::disable', 'alexf'))
		{
			$this->log('Not enough permissions to activate/deactivate user ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_country', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_country']['fields']['disable']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_country']['fields']['disable']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_country SET tstamp=". time() .", disable='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();

		// Remove the session if the user is disabled (see #5353)
		if (!$blnVisible)
		{
			$this->Database->prepare("DELETE FROM tl_session WHERE name='BE_USER_AUTH' AND pid=?")
						   ->execute($intId);
		}
	}


	public function getCountriesAsOptions()
    {
        $countries = \Contao\System::getCountriesWithFlags();
        $arr = [];

        foreach ($countries as $key => $name) {
            $arr[] = [
                'value' => $key,
                'label' => $name
            ];
        }

        return $countries;
    }
}
