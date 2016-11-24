<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Table tl_casino_category
 */
$GLOBALS['TL_DCA']['tl_casino_category'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_casino_category', 'checkPermission')
		),
		'onsubmit_callback' => array
		(
			array('tl_casino_category', 'storeDateAdded'),
			array('tl_casino_category', 'checkRemoveSession')
		),
		'ondelete_callback' => array
		(
			array('tl_casino_category', 'removeSession')
		),
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
		'sorting' => array
		(
			'fields'                  => array('dateAdded DESC'),
			'flag'                    => 1,
		),
		'label' => array
		(
			'fields'                  => array('country'),
			'callback'                => array('tl_casino_category', 'listCallback')
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_casino_category']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'icon_new'            => 'pencil',
				'button_callback'     => array('tl_casino_category', 'editUser')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_casino_category']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'icon_new'            => 'trash',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"',
				'button_callback'     => array('tl_casino_category', 'deleteUser')
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'country, name,short_name'
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['country'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'options_callback'        => ['tl_casino_category', 'getCountriesAsOptions'],
            'required'                => true,
            'eval'                    => array('mandatory'=>true, 'unique'=>true),
            'sql'                     => "varchar(5) NOT NULL"
        ),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['name'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_casino_category']['short_name'],
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

class tl_casino_category extends \Contao\Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\\BackendUser', 'User');
	}


	/**
	 * Check permissions to edit table tl_casino_category
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		// Check current action
		switch (Input::get('act'))
		{
			case 'create':
			case 'select':
			case 'show':
				// Allow
				break;

			case 'delete':
				if (Input::get('id') == $this->User->id)
				{
					$this->log('Attempt to delete own account ID "'.Input::get('id').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				// no break;

			case 'edit':
			case 'copy':
			case 'toggle':
			default:
				$objUser = $this->Database->prepare("SELECT admin FROM tl_casino_category WHERE id=?")
										  ->limit(1)
										  ->execute(Input::get('id'));

				if ($objUser->admin && Input::get('act') != '')
				{
					$this->log('Not enough permissions to '.Input::get('act').' administrator account ID "'.Input::get('id').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $this->Session->getData();
				$objUser = $this->Database->execute("SELECT id FROM tl_casino_category WHERE admin=1");
				$session['CURRENT']['IDS'] = array_diff($session['CURRENT']['IDS'], $objUser->fetchEach('id'));
				$this->Session->setData($session);
				break;
		}
	}

	public function listCallback($item)
	{
		$item['fields'][0] = \Contao\System::getCountriesWithFlags()[$item['fields'][0]];
		return $item;
	}


	/**
	 * Return the edit user button
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
	public function editUser($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || !$row['admin']) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


	/**
	 * Return the copy page button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 * @param string $table
	 *
	 * @return string
	 */
	public function copyUser($row, $href, $label, $title, $icon, $attributes, $table)
	{
		if ($GLOBALS['TL_DCA'][$table]['config']['closed'])
		{
			return '';
		}

		return ($this->User->isAdmin || !$row['admin']) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


	/**
	 * Return the delete page button
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
	public function deleteUser($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || !$row['admin']) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
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
		if (Input::post('FORM_SUBMIT') == 'tl_casino_category')
		{
			$arrPurge = Input::post('purge');

			if (is_array($arrPurge))
			{
				$this->import('Contao\\Automator', 'Automator');

				if (in_array('purge_session', $arrPurge))
				{
					$this->Session->setData(array());
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_casino_category']['sessionPurged']);
				}

				if (in_array('purge_images', $arrPurge))
				{
					$this->Automator->purgeImageCache();
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_casino_category']['htmlPurged']);
				}

				if (in_array('purge_pages', $arrPurge))
				{
					$this->Automator->purgePageCache();
					Message::addConfirmation($GLOBALS['TL_LANG']['tl_casino_category']['tempPurged']);
				}
			}
		}

		return '
<div>
  <fieldset class="tl_checkbox_container">
    <legend>'.$GLOBALS['TL_LANG']['tl_casino_category']['session'][0].'</legend>
    <input type="checkbox" id="check_all_purge" class="tl_checkbox" onclick="Backend.toggleCheckboxGroup(this, \'ctrl_purge\')"> <label for="check_all_purge" style="color:#a6a6a6"><em>'.$GLOBALS['TL_LANG']['MSC']['selectAll'].'</em></label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_0" class="tl_checkbox" value="purge_session" onfocus=""> <label for="opt_purge_0">'.$GLOBALS['TL_LANG']['tl_casino_category']['sessionLabel'].'</label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_1" class="tl_checkbox" value="purge_images" onfocus=""> <label for="opt_purge_1">'.$GLOBALS['TL_LANG']['tl_casino_category']['htmlLabel'].'</label><br>
    <input type="checkbox" name="purge[]" id="opt_purge_2" class="tl_checkbox" value="purge_pages" onfocus=""> <label for="opt_purge_2">'.$GLOBALS['TL_LANG']['tl_casino_category']['tempLabel'].'</label>
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

		$this->Database->prepare("UPDATE tl_casino_category SET dateAdded=? WHERE id=?")
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
		if (!$this->User->hasAccess('tl_casino_category::disable', 'alexf'))
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
		if (!$this->User->hasAccess('tl_casino_category::disable', 'alexf'))
		{
			$this->log('Not enough permissions to activate/deactivate user ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_casino_category', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_casino_category']['fields']['disable']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_casino_category']['fields']['disable']['save_callback'] as $callback)
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
		$this->Database->prepare("UPDATE tl_casino_category SET tstamp=". time() .", disable='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
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
