<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\Modules;

use Contao\Environment;

/**
 * Front end module "logout".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleLogout extends AbstractModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate;


	/**
	 * Logout the current user and redirect
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['logout'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set last page visited
		if ($this->redirectBack)
		{
			$_SESSION['LAST_PAGE_VISITED'] = $this->getReferer();
		}

		$this->import('Contao\\FrontendUser', 'User');
		$strRedirect = Environment::get('base');

		// Redirect to last page visited
		if ($this->redirectBack && !empty($_SESSION['LAST_PAGE_VISITED']))
		{
			$strRedirect = $_SESSION['LAST_PAGE_VISITED'];
		}

		// Redirect to jumpTo page
		elseif ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null)
		{
			/** @var \PageModel $objTarget */
			$strRedirect = $objTarget->getFrontendUrl();
		}

		// Log out and redirect
		if ($this->User->logout())
		{
			$this->redirect($strRedirect);
		}

		return '';
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		return;
	}
}
