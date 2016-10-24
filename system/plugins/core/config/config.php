<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Back end modules
 */
$GLOBALS['BE_MOD'] = array
(
	// Content modules
	'content' => array
	(
		'page' => array
		(
			'tables'      => array('tl_page', 'tl_content')
		),
		'form' => array
		(
			'tables'      => array('tl_form', 'tl_form_field')
		)
	),

	// Design modules
	'design' => array
	(
		'themes' => array
		(
			'tables'      => array('tl_theme', 'tl_module', 'tl_style_sheet', 'tl_style', 'tl_layout', 'tl_image_size', 'tl_image_size_item'),
			'importTheme' => array('Theme', 'importTheme'),
			'exportTheme' => array('Theme', 'exportTheme'),
			'import'      => array('Contao\\StyleSheets', 'importStyleSheet'),
			'export'      => array('Contao\\StyleSheets', 'exportStyleSheet')
		),
		'tpl_editor' => array
		(
			'tables'      => array('tl_templates'),
			'new_tpl'     => array('tl_templates', 'addNewTemplate'),
			'compare'     => array('tl_templates', 'compareTemplate'),
		)
	),

	// Account modules
	'accounts' => array
	(
		'member' => array
		(
			'tables'      => array('tl_member')
		),
		'mgroup' => array
		(
			'tables'      => array('tl_member_group')
		),
		'user' => array
		(
			'tables'      => array('tl_user')
		),
		'group' => array
		(
			'tables'      => array('tl_user_group')
		)
	),

	// System modules
	'system' => array
	(
		'files' => array
		(
			'tables'      => array('tl_files')
		),
		'log' => array
		(
			'tables'      => array('tl_log')
		),
		'settings' => array
		(
			'tables'      => array('tl_settings')
		),
		'maintenance' => array
		(
			'callback'    => 'Contao\\FileManager'
		),
		'undo' => array
		(
			'tables'      => array('tl_undo')
		)
	)
);


/**
 * Front end modules
 */
$GLOBALS['FE_MOD'] = array
(
	'navigationMenu' => array
	(
		'navigation'     => 'Contao\\Modules\\ModuleNavigation',
		'customnav'      => 'Contao\\Modules\\ModuleCustomnav',
		'breadcrumb'     => 'Contao\\Modules\\ModuleBreadcrumb',
		'quicknav'       => 'Contao\\Modules\\ModuleQuicknav',
		'quicklink'      => 'Contao\\Modules\\ModuleQuicklink',
		'booknav'        => 'Contao\\Modules\\ModuleBooknav',
		'sitemap'        => 'Contao\\Modules\\ModuleSitemap'
	),
	'user' => array
	(
		'login'          => 'Contao\\Modules\\ModuleLogin',
		'logout'         => 'Contao\\Modules\\ModuleLogout',
		'personalData'   => 'Contao\\Modules\\ModulePersonalData',
		'registration'   => 'Contao\\Modules\\ModuleRegistration',
		'changePassword' => 'Contao\\Modules\\ModuleChangePassword',
		'lostPassword'   => 'Contao\\Modules\\ModulePassword',
		'closeAccount'   => 'Contao\\Modules\\ModuleCloseAccount'
	),
	'application' => array
	(
		'form'           => 'Form',
		'search'         => 'Contao\\Modules\\ModuleSearch'
	),
	'miscellaneous' => array
	(
		'flash'          => 'Contao\\Modules\\ModuleFlash',
		'html'           => 'Contao\\Modules\\ModuleHtml',
		'rss_reader'     => 'Contao\\Modules\\ModuleRssReader'
	)
);


/**
 * Content elements
 */
$GLOBALS['TL_CTE'] = array
(
	'texts' => array
	(
		'headline'        => 'Contao\\Elements\\ContentHeadline',
		'text'            => 'Contao\\Elements\\ContentText',
		'html'            => 'Contao\\Elements\\ContentHtml',
		'list'            => 'Contao\\Elements\\ContentList',
		'table'           => 'Contao\\Elements\\ContentTable',
		'code'            => 'Contao\\Elements\\ContentCode',
		'markdown'        => 'Contao\\Elements\\ContentMarkdown'
	),
	'slider' => array
	(
		'sliderStart'     => 'Contao\\Elements\\ContentSliderStart',
		'sliderStop'      => 'Contao\\Elements\\ContentSliderStop'
	),
	'links' => array
	(
		'hyperlink'       => 'Contao\\Elements\\ContentHyperlink',
		'toplink'         => 'Contao\\Elements\\ContentToplink'
	),
	'media' => array
	(
		'image'           => 'Contao\\Elements\\ContentImage',
		'player'          => 'Contao\\Elements\\ContentMedia',
		'youtube'         => 'Contao\\Elements\\ContentYouTube'
	),
	'files' => array
	(
		'download'        => 'Contao\\Elements\\ContentDownload',
		'downloads'       => 'Contao\\Elements\\ContentDownloads'
	),
	'includes' => array
	(
		'alias'           => 'Contao\\Elements\\ContentAlias',
		'form'            => 'Contao\\Elements\\Form',
		'module'          => 'Contao\\Elements\\ContentModule',
	)
);

/**
 * Back end form fields
 */
$GLOBALS['BE_FFL'] = array
(
	'text'           => 'Contao\\Editors\\TextField',
	'password'       => 'Contao\\Editors\\Password',
	'textStore'      => 'Contao\\Editors\\TextStore',
	'textarea'       => 'Contao\\Editors\\TextArea',
	'select'         => 'Contao\\Editors\\SelectMenu',
	'checkbox'       => 'Contao\\Editors\\CheckBox',
	'checkboxWizard' => 'Contao\\Editors\\CheckBoxWizard',
	'radio'          => 'Contao\\Editors\\RadioButton',
	'radioTable'     => 'Contao\\Editors\\RadioTable',
	'inputUnit'      => 'Contao\\Editors\\InputUnit',
	'trbl'           => 'Contao\\Editors\\TrblField',
	'chmod'          => 'Contao\\Editors\\ChmodTable',
	'pageTree'       => 'Contao\\Editors\\PageTree',
	'pageSelector'   => 'Contao\\Editors\\PageSelector',
	'fileTree'       => 'Contao\\Editors\\FileTree',
	'fileSelector'   => 'Contao\\Editors\\FileSelector',
	'fileUpload'     => 'Contao\\Editors\\Upload',
	'tableWizard'    => 'Contao\\Editors\\TableWizard',
	'listWizard'     => 'Contao\\Editors\\ListWizard',
	'optionWizard'   => 'Contao\\Editors\\OptionWizard',
	'moduleWizard'   => 'Contao\\Editors\\ModuleWizard',
	'keyValueWizard' => 'Contao\\Editors\\KeyValueWizard',
	'imageSize'      => 'Contao\\Editors\\ImageSize',
	'timePeriod'     => 'Contao\\Editors\\TimePeriod',
	'metaWizard'     => 'Contao\\Editors\\MetaWizard'
);


/**
 * Front end form fields
 */
$GLOBALS['TL_FFL'] = array
(
	'headline'    => 'FormHeadline',
	'explanation' => 'FormExplanation',
	'html'        => 'FormHtml',
	'fieldset'    => 'FormFieldset',
	'text'        => 'FormTextField',
	'password'    => 'FormPassword',
	'textarea'    => 'FormTextArea',
	'select'      => 'FormSelectMenu',
	'radio'       => 'FormRadioButton',
	'checkbox'    => 'FormCheckBox',
	'upload'      => 'FormFileUpload',
	'hidden'      => 'FormHidden',
	'captcha'     => 'FormCaptcha',
	'submit'      => 'FormSubmit'
);


/**
 * Page types
 */
$GLOBALS['TL_PTY'] = array
(
	'regular'   => 'Contao\\Pages\\PageRegular',
	'forward'   => 'Contao\\Pages\\PageForward',
	'redirect'  => 'Contao\\Pages\\PageRedirect',
	'root'      => 'Contao\\Pages\\PageRoot',
	'error_403' => 'Contao\\Pages\\PageError403',
	'error_404' => 'Contao\\Pages\\PageError404'
);


/**
 * Maintenance
 */
$GLOBALS['TL_MAINTENANCE'] = array
(
	'Contao\\RebuildIndex',
	'Contao\\PurgeData'
);


/**
 * Purge jobs
 */
$GLOBALS['TL_PURGE'] = array
(
	'tables' => array
	(
		'index' => array
		(
			'callback' => array('Contao\\Automator', 'purgeSearchTables'),
			'affected' => array('tl_search', 'tl_search_index')
		),
		'undo' => array
		(
			'callback' => array('Contao\\Automator', 'purgeUndoTable'),
			'affected' => array('tl_undo')
		),
		'versions' => array
		(
			'callback' => array('Contao\\Automator', 'purgeVersionTable'),
			'affected' => array('tl_version')
		),
		'log' => array
		(
			'callback' => array('Contao\\Automator', 'purgeSystemLog'),
			'affected' => array('tl_log')
		)
	),
	'folders' => array
	(
		'images' => array
		(
			'callback' => array('Contao\\Automator', 'purgeImageCache'),
			'affected' => array('assets/images')
		),
		'scripts' => array
		(
			'callback' => array('Contao\\Automator', 'purgeScriptCache'),
			'affected' => array('assets/js', 'assets/css')
		),
		'pages' => array
		(
			'callback' => array('Contao\\Automator', 'purgePageCache'),
			'affected' => array('system/cache/html')
		),
		'search' => array
		(
			'callback' => array('Contao\\Automator', 'purgeSearchCache'),
			'affected' => array('system/cache/search')
		),
		'internal' => array
		(
			'callback' => array('Contao\\Automator', 'purgeInternalCache'),
			'affected' => array('system/cache/config', 'system/cache/dca', 'system/cache/language', 'system/cache/sql')
		),
		'temp' => array
		(
			'callback' => array('Contao\\Automator', 'purgeTempFolder'),
			'affected' => array('system/tmp')
		)
	),
	'custom' => array
	(
		'xml' => array
		(
			'callback' => array('Contao\\Automator', 'generateXmlFiles')
		)
	)
);


/**
 * Image crop modes
 */
$GLOBALS['TL_CROP'] = array
(
	'relative' => array
	(
		'proportional', 'box'
	),
	'exact' => array
	(
		'crop',
		'left_top',    'center_top',    'right_top',
		'left_center', 'center_center', 'right_center',
		'left_bottom', 'center_bottom', 'right_bottom'
	)
);


/**
 * Cron jobs
 */
$GLOBALS['TL_CRON'] = array
(
	'monthly' => array
	(
		array('Contao\\Automator', 'purgeImageCache')
	),
	'weekly' => array
	(
		array('Contao\\Automator', 'generateSitemap'),
		array('Contao\\Automator', 'purgeScriptCache'),
		array('Contao\\Automator', 'purgeSearchCache')
	),
	'daily' => array
	(
		array('Contao\\Automator', 'rotateLogs'),
		array('Contao\\Automator', 'purgeTempFolder'),
		array('Contao\\Automator', 'checkForUpdates')
	),
	'hourly' => array(),
	'minutely' => array()
);


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS'] = array
(
	'getSystemMessages' => array
	(
		array('Contao\\Messages', 'versionCheck'),
		array('Contao\\Messages', 'lastLogin'),
		array('Contao\\Messages', 'topLevelRoot'),
		array('Contao\\Messages', 'languageFallback')
	)
);


/**
 * Register the auto_item keywords
 */
$GLOBALS['TL_AUTO_ITEM'] = array('items', 'events');


/**
 * Do not index a page if one of the following parameters is set
 */
$GLOBALS['TL_NOINDEX_KEYS'] = array('id', 'file', 'token', 'day', 'month', 'year', 'page', 'PHPSESSID');


/**
 * Register the supported CSS units
 */
$GLOBALS['TL_CSS_UNITS'] = array('px', '%', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', 'ex', 'pt', 'pc', 'in', 'cm', 'mm');


/**
 * Asset versions
 */
$GLOBALS['TL_ASSETS'] = array
(
	'ACE'          => '1.1.8',
	'CSS3PIE'      => '1.0.0',
	'DROPZONE'     => '3.12.0',
	'HIGHLIGHTER'  => '3.0.83',
	'HTML5SHIV'    => '3.7.2',
	'RESPIMAGE'    => '1.4.0',
	'SWIPE'        => '2.0',
	'JQUERY'       => '1.11.3',
	'JQUERY_UI'    => '1.11.4',
	'COLORBOX'     => '1.6.1',
	'MEDIAELEMENT' => '2.21.2',
	'TABLESORTER'  => '2.0.5',
	'MOOTOOLS'     => '1.5.2',
	'COLORPICKER'  => '1.4',
	'DATEPICKER'   => '2.2.0',
	'MEDIABOX'     => '1.4.6',
);


/**
 * Other global arrays
 */
$GLOBALS['TL_MODELS'] = [];
$GLOBALS['TL_PERMISSIONS'] = [];

$GLOBALS['BE_MENU'] = [];


$GLOBALS['TL_MODELS']['tl_content'] = 'Contao\\Models\\ContentModel';
$GLOBALS['TL_MODELS']['tl_user'] = 'Contao\\Models\\UserModel';
$GLOBALS['TL_MODELS']['tl_user_group'] = 'Contao\\Models\\UserGroupModel';
$GLOBALS['TL_MODELS']['tl_theme'] = 'Contao\\Models\\ThemeModel';
$GLOBALS['TL_MODELS']['tl_style_sheet'] = 'Contao\\Models\\StyleSheetModel';
$GLOBALS['TL_MODELS']['tl_style'] = 'Contao\\Models\\StyleModel';
$GLOBALS['TL_MODELS']['tl_session'] = 'Contao\\Models\\SessionModel';
$GLOBALS['TL_MODELS']['tl_page'] = 'Contao\\Models\\PageModel';
$GLOBALS['TL_MODELS']['tl_module'] = 'Contao\\Models\\ModuleModel';
$GLOBALS['TL_MODELS']['tl_member'] = 'Contao\\Models\\MemberModel';
$GLOBALS['TL_MODELS']['tl_member_group'] = 'Contao\\Models\\MemberGroupModel';
$GLOBALS['TL_MODELS']['tl_layout'] = 'Contao\\Models\\LayoutModel';
$GLOBALS['TL_MODELS']['tl_image_size'] = 'Contao\\Models\\ImageSizeModel';
$GLOBALS['TL_MODELS']['tl_image_size_item'] = 'Contao\\Models\\ImageSizeItemModel';
$GLOBALS['TL_MODELS']['tl_form'] = 'Contao\\Models\\FormModel';
$GLOBALS['TL_MODELS']['tl_form_field'] = 'Contao\\Models\\FormFieldModel';
$GLOBALS['TL_MODELS']['tl_files'] = 'Contao\\Models\\FilesModel';
