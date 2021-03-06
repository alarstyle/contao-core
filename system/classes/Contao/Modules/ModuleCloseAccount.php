<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\Modules;

use Contao\Encryption;
use Contao\Environment;
use Contao\Idna;
use Contao\Input;
use Contao\Models\MemberModel;
use Contao\Forms\FormTextField;

/**
 * Front end module "close account".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleCloseAccount extends AbstractModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_password';


	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['closeAccount'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Return if there is no logged in user
		if (!FE_USER_LOGGED_IN)
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->import('Contao\\FrontendUser', 'User');

		// Initialize the password editor
		$arrField = array
		(
			'name' => 'password',
			'inputType' => 'text',
			'label' => $GLOBALS['TL_LANG']['MSC']['password'][0],
			'eval' => array('hideInput'=>true, 'preserveTags'=>true, 'mandatory'=>true, 'required'=>true, 'tableless'=>$this->tableless)
		);

		$objEditor = new FormTextField(FormTextField::getAttributesFromDca($arrField, $arrField['name']));
		$objEditor->rowClass = 'row_0 row_first even';

		// Validate editor
		if (Input::post('FORM_SUBMIT') == 'tl_close_account')
		{
			$objEditor->validate();

			// Validate the password
			if (!$objEditor->hasErrors())
			{
				// The password has been generated with crypt()
				if (Encryption::test($this->User->password))
				{
					$blnAuthenticated = Encryption::verify($objEditor->value, $this->User->password);
				}
				else
				{
					list($strPassword, $strSalt) = explode(':', $this->User->password);
					$blnAuthenticated = ($strSalt == '') ? ($strPassword === sha1($objEditor->value)) : ($strPassword === sha1($strSalt . $objEditor->value));
				}

				if (!$blnAuthenticated)
				{
					$objEditor->value = '';
					$objEditor->addError($GLOBALS['TL_LANG']['ERR']['invalidPass']);
				}
			}

			// Close account
			if (!$objEditor->hasErrors())
			{
				// HOOK: send account ID
				if (isset($GLOBALS['TL_HOOKS']['closeAccount']) && is_array($GLOBALS['TL_HOOKS']['closeAccount']))
				{
					foreach ($GLOBALS['TL_HOOKS']['closeAccount'] as $callback)
					{
						$this->import($callback[0]);
						$this->{$callback[0]}->{$callback[1]}($this->User->id, $this->reg_close, $this);
					}
				}

				$objMember = MemberModel::findByPk($this->User->id);

				// Remove the account
				if ($this->reg_close == 'close_delete')
				{
					$objMember->delete();
					$this->log('User account ID ' . $this->User->id . ' (' . Idna::decodeEmail($this->User->email) . ') has been deleted', __METHOD__, TL_ACCESS);
				}
				// Deactivate the account
				else
				{
					$objMember->disable = 1;
					$objMember->tstamp = time();
					$objMember->save();
					$this->log('User account ID ' . $this->User->id . ' (' . Idna::decodeEmail($this->User->email) . ') has been deactivated', __METHOD__, TL_ACCESS);
				}

				$this->User->logout();

				// Check whether there is a jumpTo page
				if (($objJumpTo = $this->objModel->getRelated('jumpTo')) !== null)
				{
					$this->jumpToOrReload($objJumpTo->row());
				}

				$this->reload();
			}
		}

		$this->Template->fields = $objEditor->parse();

		$this->Template->formId = 'tl_close_account';
		$this->Template->action = Environment::get('indexFreeRequest');
		$this->Template->slabel = specialchars($GLOBALS['TL_LANG']['MSC']['closeAccount']);
		$this->Template->rowLast = 'row_1 row_last odd';
		$this->Template->tableless = $this->tableless;
	}
}
