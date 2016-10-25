<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\Modules;

use Contao\Email;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Idna;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;
use Contao\Models\MemberModel;

/**
 * Front end module "lost password".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModulePassword extends AbstractModule
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

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['lostPassword'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var \PageModel $objPage */
		global $objPage;

		$GLOBALS['TL_LANGUAGE'] = $objPage->language;

		System::loadLanguageFile('tl_member');
		$this->loadDataContainer('tl_member');

		// Set new password
		if (strlen(Input::get('token')))
		{
			$this->setNewPassword();

			return;
		}

		// Username editor
		if (!$this->reg_skipName)
		{
			$arrFields['username'] = $GLOBALS['TL_DCA']['tl_member']['fields']['username'];
			$arrFields['username']['name'] = 'username';
		}

		// E-mail editor
		$arrFields['email'] = $GLOBALS['TL_DCA']['tl_member']['fields']['email'];
		$arrFields['email']['name'] = 'email';

		// Captcha editor
		if (!$this->disableCaptcha)
		{
			$arrFields['captcha'] = array
			(
				'name' => 'lost_password',
				'label' => $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
				'inputType' => 'captcha',
				'eval' => array('mandatory'=>true)
			);
		}

		$row = 0;
		$strFields = '';
		$doNotSubmit = false;

		// Initialize the editors
		foreach ($arrFields as $arrField)
		{
			/** @var \Editor $strClass */
			$strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];

			// Continue if the class is not defined
			if (!class_exists($strClass))
			{
				continue;
			}

			$arrField['eval']['tableless'] = $this->tableless;
			$arrField['eval']['required'] = $arrField['eval']['mandatory'];

			/** @var \Editor $objEditor */
			$objEditor = new $strClass($strClass::getAttributesFromDca($arrField, $arrField['name']));

			$objEditor->storeValues = true;
			$objEditor->rowClass = 'row_' . $row . (($row == 0) ? ' row_first' : '') . ((($row % 2) == 0) ? ' even' : ' odd');
			++$row;

			// Validate the editor
			if (Input::post('FORM_SUBMIT') == 'tl_lost_password')
			{
				$objEditor->validate();

				if ($objEditor->hasErrors())
				{
					$doNotSubmit = true;
				}
			}

			$strFields .= $objEditor->parse();
		}

		$this->Template->fields = $strFields;
		$this->Template->hasError = $doNotSubmit;

		// Look for an account and send the password link
		if (Input::post('FORM_SUBMIT') == 'tl_lost_password' && !$doNotSubmit)
		{
			if ($this->reg_skipName)
			{
				$objMember = MemberModel::findActiveByEmailAndUsername(Input::post('email', true), null);
			}
			else
			{
				$objMember = MemberModel::findActiveByEmailAndUsername(Input::post('email', true), Input::post('username'));
			}

			if ($objMember === null)
			{
				sleep(2); // Wait 2 seconds while brute forcing :)
				$this->Template->error = $GLOBALS['TL_LANG']['MSC']['accountNotFound'];
			}
			else
			{
				$this->sendPasswordLink($objMember);
			}
		}

		$this->Template->formId = 'tl_lost_password';
		$this->Template->username = specialchars($GLOBALS['TL_LANG']['MSC']['username']);
		$this->Template->email = specialchars($GLOBALS['TL_LANG']['MSC']['emailAddress']);
		$this->Template->action = Environment::get('indexFreeRequest');
		$this->Template->slabel = specialchars($GLOBALS['TL_LANG']['MSC']['requestPassword']);
		$this->Template->rowLast = 'row_' . $row . ' row_last' . ((($row % 2) == 0) ? ' even' : ' odd');
		$this->Template->tableless = $this->tableless;
	}


	/**
	 * Set the new password
	 */
	protected function setNewPassword()
	{
		$objMember = MemberModel::findOneByActivation(Input::get('token'));

		if ($objMember === null || $objMember->login == '')
		{
			$this->strTemplate = 'mod_message';

			/** @var \FrontendTemplate|object $objTemplate */
			$objTemplate = new FrontendTemplate($this->strTemplate);

			$this->Template = $objTemplate;
			$this->Template->type = 'error';
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['accountError'];

			return;
		}

		$strTable = $objMember->getTable();

		// Initialize the versioning (see #8301)
		$objVersions = new Versions($strTable, $objMember->id);
		$objVersions->setUsername($objMember->username);
		$objVersions->setUserId(0);
		$objVersions->setEditUrl('contao/main.php?do=member&act=edit&id=%s&rt=1');
		$objVersions->initialize();

		// Define the form field
		$arrField = $GLOBALS['TL_DCA']['tl_member']['fields']['password'];
		$arrField['eval']['tableless'] = $this->tableless;

		/** @var \Editor $strClass */
		$strClass = $GLOBALS['TL_FFL']['password'];

		// Fallback to default if the class is not defined
		if (!class_exists($strClass))
		{
			$strClass = 'Contao\\Forms\\FormPassword';
		}

		/** @var \Editor $objEditor */
		$objEditor = new $strClass($strClass::getAttributesFromDca($arrField, 'password'));

		// Set row classes
		$objEditor->rowClass = 'row_0 row_first even';
		$objEditor->rowClassConfirm = 'row_1 odd';
		$this->Template->rowLast = 'row_2 row_last even';

		// Validate the field
		if (strlen(Input::post('FORM_SUBMIT')) && Input::post('FORM_SUBMIT') == $this->Session->get('setPasswordToken'))
		{
			$objEditor->validate();

			// Set the new password and redirect
			if (!$objEditor->hasErrors())
			{
				$this->Session->set('setPasswordToken', '');

				$objMember->tstamp = time();
				$objMember->activation = '';
				$objMember->password = $objEditor->value;
				$objMember->save();

				// Create a new version
				if ($GLOBALS['TL_DCA'][$strTable]['config']['enableVersioning'])
				{
					$objVersions->create();
				}

				// HOOK: set new password callback
				if (isset($GLOBALS['TL_HOOKS']['setNewPassword']) && is_array($GLOBALS['TL_HOOKS']['setNewPassword']))
				{
					foreach ($GLOBALS['TL_HOOKS']['setNewPassword'] as $callback)
					{
						$this->import($callback[0]);
						$this->{$callback[0]}->{$callback[1]}($objMember, $objEditor->value, $this);
					}
				}

				// Redirect to the jumpTo page
				if (($objTarget = $this->objModel->getRelated('reg_jumpTo')) !== null)
				{
					/** @var \PageModel $objTarget */
					$this->redirect($objTarget->getFrontendUrl());
				}

				// Confirm
				$this->strTemplate = 'mod_message';

				/** @var \FrontendTemplate|object $objTemplate */
				$objTemplate = new FrontendTemplate($this->strTemplate);

				$this->Template = $objTemplate;
				$this->Template->type = 'confirm';
				$this->Template->message = $GLOBALS['TL_LANG']['MSC']['newPasswordSet'];

				return;
			}
		}

		$strToken = md5(uniqid(mt_rand(), true));
		$this->Session->set('setPasswordToken', $strToken);

		$this->Template->formId = $strToken;
		$this->Template->fields = $objEditor->parse();
		$this->Template->action = Environment::get('indexFreeRequest');
		$this->Template->slabel = specialchars($GLOBALS['TL_LANG']['MSC']['setNewPassword']);
		$this->Template->tableless = $this->tableless;
	}


	/**
	 * Create a new user and redirect
	 *
	 * @param \MemberModel $objMember
	 */
	protected function sendPasswordLink($objMember)
	{
		$confirmationId = md5(uniqid(mt_rand(), true));

		// Store the confirmation ID
		$objMember = MemberModel::findByPk($objMember->id);
		$objMember->activation = $confirmationId;
		$objMember->save();

		// Prepare the simple token data
		$arrData = $objMember->row();
		$arrData['domain'] = Idna::decode(Environment::get('host'));
		$arrData['link'] = Idna::decode(Environment::get('base')) . Environment::get('request') . (strpos(Environment::get('request'), '?') !== false ? '&' : '?') . 'token=' . $confirmationId;

		// Send e-mail
		$objEmail = new Email();

		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['passwordSubject'], Idna::decode(Environment::get('host')));
		$objEmail->text = StringUtil::parseSimpleTokens($this->reg_password, $arrData);
		$objEmail->sendTo($objMember->email);

		$this->log('A new password has been requested for user ID ' . $objMember->id . ' (' . Idna::decodeEmail($objMember->email) . ')', __METHOD__, TL_ACCESS);

		// Check whether there is a jumpTo page
		if (($objJumpTo = $this->objModel->getRelated('jumpTo')) !== null)
		{
			$this->jumpToOrReload($objJumpTo->row());
		}

		$this->reload();
	}
}
