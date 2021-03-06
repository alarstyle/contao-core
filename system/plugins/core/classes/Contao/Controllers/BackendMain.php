<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\Controllers;

use Contao\Ajax;
use Contao\Backend;
use Contao\BackendSections;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\Lang;
use Contao\Message;
use Contao\Models\ArticleModel;
use Contao\System;
use Contao\Versions;
use Grow\ApplicationData;
use Grow\Route;

/**
 * Main back end controller.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class BackendMain extends Backend
{

	/**
	 * Current Ajax object
	 * @var Ajax
	 */
	protected $objAjax;

	/**
	 * @var BackendTemplate|object
	 */
	protected $Template;


	/**
	 * Initialize the controller
	 *
	 * 1. Import the user
	 * 2. Call the parent constructor
	 * 3. Authenticate the user
	 * 4. Load the language files
	 * DO NOT CHANGE THIS ORDER!
	 */
	public function __construct()
	{
		$this->import('Contao\\BackendUser', 'User');
		parent::__construct();

		// Password change required
		if ($this->User->pwChange)
		{
			$objSession = $this->Database->prepare("SELECT su FROM tl_session WHERE hash=?")
										 ->execute(sha1(session_id() . (!Config::get('disableIpCheck') ? Environment::get('ip') : '') . 'BE_USER_AUTH'));

			if (!$objSession->su)
			{
				$this->redirect('contao/password.php');
			}
		}

		// Front end redirect
		if (Input::get('do') == 'feRedirect')
		{
			$this->redirectToFrontendPage(Input::get('page'), Input::get('article'));
		}

		// Convenience functions
		if ($this->User->isAdmin)
		{
			// Safe mode off
			if (Input::get('smo'))
			{
				$this->import('Contao\\Automator', 'Automator');
				$this->Automator->purgeInternalCache();
				Config::persist('coreOnlyMode', false);
				$this->redirect($this->getReferer());
			}

			// Maintenance mode off
			if (Input::get('mmo'))
			{
				Config::persist('maintenanceMode', false);
				$this->redirect($this->getReferer());
			}

			// Build internal cache
			if (Input::get('bic'))
			{
				$this->import('Contao\\Automator', 'Automator');
				$this->Automator->generateInternalCache();
				$this->redirect($this->getReferer());
			}
		}

		System::loadLanguageFile('default');
		System::loadLanguageFile('modules');
	}


	/**
	 * Run the controller and parse the login template
	 */
	public function run()
	{
		$this->Template = new BackendTemplate('be_main');
		$this->Template->main = '';

        $route = Route::get();

		$this->Template->pageClass = 'page_' . $route[1];

		// Ajax request
		if ($_POST && Environment::get('isAjaxRequest'))
		{
			$this->objAjax = new Ajax(Input::post('action'));
			$this->objAjax->executePreActions();
		}

		// Error
		if (Input::get('act') == 'error')
		{
			$this->Template->error = $GLOBALS['TL_LANG']['ERR']['general'];
			$this->Template->title = $GLOBALS['TL_LANG']['ERR']['general'];
		}
		else
		{
			$this->generateMainSection();
		}

		$this->output();
	}


	protected function generateMainSection()
    {
        // Welcome screen
        if (!Input::get('do') && !Input::get('act'))
        {
            $this->Template->main .= $this->welcomeScreen();
            $this->Template->title = $GLOBALS['TL_LANG']['MSC']['home'];
        }
        // Open a module
        elseif (Input::get('do'))
        {
            $this->Template->main .= $this->getBackendModule(Input::get('do'));
            $this->Template->title = $this->Template->headline;
        }
    }


	/**
	 * Add the welcome screen
	 *
	 * @return string
	 */
	protected function welcomeScreen()
	{
		System::loadLanguageFile('explain');

		/** @var BackendTemplate|object $objTemplate */
		$objTemplate = new BackendTemplate('be_welcome');
		$objTemplate->messages = Message::generate(false, true);

		// HOOK: add custom messages
		if (isset($GLOBALS['TL_HOOKS']['getSystemMessages']) && is_array($GLOBALS['TL_HOOKS']['getSystemMessages']))
		{
			$arrMessages = array();

			foreach ($GLOBALS['TL_HOOKS']['getSystemMessages'] as $callback)
			{
				$this->import($callback[0]);
				$strBuffer = $this->{$callback[0]}->{$callback[1]}();

				if ($strBuffer != '')
				{
					$arrMessages[] = $strBuffer;
				}
			}

			if (!empty($arrMessages))
			{
				$objTemplate->messages .= "\n" . implode("\n", $arrMessages);
			}
		}

		// Add the versions overview
		Versions::addToTemplate($objTemplate);

		$objTemplate->welcome = sprintf($GLOBALS['TL_LANG']['MSC']['welcomeTo'], Config::get('websiteTitle'));
		$objTemplate->showDifferences = specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MSC']['showDifferences']));
		$objTemplate->systemMessages = $GLOBALS['TL_LANG']['MSC']['systemMessages'];
		$objTemplate->shortcuts = $GLOBALS['TL_LANG']['MSC']['shortcuts'][0];
		$objTemplate->shortcutsLink = $GLOBALS['TL_LANG']['MSC']['shortcuts'][1];
		$objTemplate->editElement = specialchars($GLOBALS['TL_LANG']['MSC']['editElement']);

		return $objTemplate->parse();
	}


	/**
	 * Output the template file
	 */
	protected function output()
	{
		// Default headline
		if ($this->Template->headline == '')
		{
			$this->Template->headline = Config::get('websiteTitle');
		}

		// Default title
		if ($this->Template->title == '')
		{
			$this->Template->title = $this->Template->headline;
		}

		// File picker reference
		if (Input::get('popup') && Input::get('act') != 'show' && (Input::get('do') == 'page' && $this->User->hasAccess('page', 'modules') || Input::get('do') == 'files' && $this->User->hasAccess('files', 'modules')) && $this->Session->get('filePickerRef'))
		{
			$this->Template->managerHref = ampersand($this->Session->get('filePickerRef'));
			$this->Template->manager = (strpos($this->Session->get('filePickerRef'), 'contao/page.php') !== false) ? $GLOBALS['TL_LANG']['MSC']['pagePickerHome'] : $GLOBALS['TL_LANG']['MSC']['filePickerHome'];
		}

		$this->Template->headerMenu = '';
		foreach(BackendSections::get('HEADER_MENU') as $headerMenuItem) {
			if (is_callable($headerMenuItem)) {
				$this->Template->headerMenu .= '<li>' . $headerMenuItem() . '</li>';
			} elseif (is_string($headerMenuItem)) {
				$this->Template->headerMenu .= '<li>' . $headerMenuItem . '</li>';
			}
		}

		$this->Template->theme = Backend::getTheme();
		$this->Template->base = Environment::get('base');
		$this->Template->language = $GLOBALS['TL_LANGUAGE'];
		$this->Template->title = specialchars($this->Template->title);
		$this->Template->charset = Config::get('characterSet');
		$this->Template->account = $GLOBALS['TL_LANG']['MOD']['login'][1];
		$this->Template->preview = $GLOBALS['TL_LANG']['MSC']['fePreview'];
		$this->Template->previewTitle = specialchars($GLOBALS['TL_LANG']['MSC']['fePreviewTitle']);
		$this->Template->pageOffset = Input::cookie('BE_PAGE_OFFSET');

		$this->Template->backendModules = Lang::get('MSC::backendModules');
		$this->Template->username = $GLOBALS['TL_LANG']['MSC']['user'] . ' ' . $GLOBALS['TL_USERNAME'];
		$this->Template->request = ampersand(Environment::get('request'));
		$this->Template->top = $GLOBALS['TL_LANG']['MSC']['backToTop'];
		$this->Template->modules = $this->User->navigation();
		$this->Template->backToTop = specialchars($GLOBALS['TL_LANG']['MSC']['backToTopTitle']);
		$this->Template->expandNode = $GLOBALS['TL_LANG']['MSC']['expandNode'];
		$this->Template->collapseNode = $GLOBALS['TL_LANG']['MSC']['collapseNode'];
		$this->Template->loadingData = $GLOBALS['TL_LANG']['MSC']['loadingData'];
		$this->Template->isAdmin = $this->User->isAdmin;
		$this->Template->isCoreOnlyMode = Config::get('coreOnlyMode');
		$this->Template->coreOnlyMode = $GLOBALS['TL_LANG']['MSC']['coreOnlyMode'];
		$this->Template->coreOnlyOff = specialchars($GLOBALS['TL_LANG']['MSC']['coreOnlyOff']);
		$this->Template->coreOnlyHref = $this->addToUrl('smo=1');
		$this->Template->isMaintenanceMode = Config::get('maintenanceMode');
		$this->Template->maintenanceMode = $GLOBALS['TL_LANG']['MSC']['maintenanceMode'];
		$this->Template->maintenanceOff = specialchars($GLOBALS['TL_LANG']['MSC']['maintenanceOff']);
		$this->Template->maintenanceHref = $this->addToUrl('mmo=1');
		$this->Template->buildCacheLink = $GLOBALS['TL_LANG']['MSC']['buildCacheLink'];
		$this->Template->buildCacheText = Lang::get('MSC::buildCacheText');
		$this->Template->buildCacheHref = $this->addToUrl('bic=1');
		$this->Template->isPopup = Input::get('popup');

        if (!empty($this->jsAppClassName)) {
            ApplicationData::addData('jsAppClassName', $this->jsAppClassName);
        }

		// Hide the cache message in the repository manager (see #5966)
		if (!Config::get('bypassCache') && $this->User->isAdmin)
		{
			$this->Template->needsCacheBuild = ((Input::get('do') != 'repository_manager' || !isset($_GET['install']) && !isset($_GET['uninstall']) && !isset($_GET['update'])) && !is_dir(TL_ROOT . '/system/cache/dca'));
		}

		// Front end preview links
		if (defined('CURRENT_ID') && CURRENT_ID != '')
		{
			// Pages
			if (Input::get('do') == 'page')
			{
				$this->Template->frontendFile = '?page=' . CURRENT_ID;
			}

			// Articles
			elseif (Input::get('do') == 'article')
			{
				if (($objArticle = ArticleModel::findByPk(CURRENT_ID)) !== null)
				{
					$this->Template->frontendFile = '?page=' . $objArticle->pid;
				}
			}
		}

        $this->Template->appData = ApplicationData::dataAsJson();

		$this->Template->output();
	}
}
