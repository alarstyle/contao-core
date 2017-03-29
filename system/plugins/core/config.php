<?php


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


$GLOBALS['NAVIGATION'] = [

    'dashboard' => [
        'label' => 'Dashboard',
        'controller' => 'Grow\\Controllers\\Dashboard'
    ],

    'users' => [
        'label' => 'Users',
        'controller' => 'Grow\\Controllers\\ListingWithGroups',
        'config' => [
            'group' => [
                'table' => 'tl_user_group',
                'title' => 'Groups',
                'labelAll' => 'All Users',
                'labelNew' => 'Add Group',
                'creatable' => true,
                'editable' => true,
                'labelCallback' => function ($item) {
                    return $item->name;
                },
                'titleCallback' => function ($item) {
                    return $item->name;
                },
                'sortingCallback' => function ($groups) {
                    usort($groups, function ($a, $b) {
                        if ($a['title'] === $b['title']) {
                            return 0;
                        }
                        return (strtolower($a['title']) < strtolower($b['title'])) ? -1 : 1;
                    });
                    return $groups;
                }
            ],
            'list' => [
                'table' => 'tl_user',
                'title' => 'Users',
                'labelNew' => 'Add New User',
                'labelEdit' => 'Edit User',
                'creatable' => true,
                'whereCallback' => function() {
                    $groupId = \Contao\Input::post('groupId');
                    if (empty($groupId) || $groupId === 'all') {
                        return [];
                    }
                    return [['groups', 'like', '%"' . $groupId . '"%']];
                },
                'headersCallback' => function($headers) {
                    foreach($headers as &$header) {
                        switch($header['name']) {
                            case 'avatar':
                                $header['label'] = '';
                        }
                    }
                    return $headers;
                },
                'listCallback' => function($list) {
                    foreach ($list as $i=>$item) {
                        $list[$i]['fields'][0] = '<div class="user_avatar" style="background-image: url(\'' . $list[$i]['fields'][0] . '\')"></div>';
                        $countriesIds = deserialize($list[$i]['fields'][3]);
                        if (empty($countriesIds)) {
                            $list[$i]['fields'][3] = '';
                        } else {
                            $list[$i]['fields'][3] = \Gambling\BackendHelpers::getCountriesFlagsByIds($countriesIds);
                        }
                    }
                    return $list;
                }
            ]
        ]
    ],

    'filemanager' => [
        'label' => 'Filemanager',
        'controller' => 'Grow\\Controllers\\FileManagerController'
    ],

//    'setting' => [
//        'label' => 'Setting',
//        'controller' => 'Grow\\Controllers\\Settings'
//    ]
    
];


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
 * UNITS
 */
$GLOBALS['UNITS'] = [
	'text'           => 'Grow\\Units\\Text',
	'password'       => 'Grow\\Units\\Password',
	'textarea'       => 'Grow\\Units\\Textarea',
    'select'         => 'Grow\\Units\\Select',
    'checkbox'       => 'Grow\\Units\\Checkbox',
    'checkboxWizard' => 'Grow\\Units\\CheckboxWizard',
    'radio'          => 'Grow\\Units\\Radio',
    'radioTable'     => 'Grow\\Units\\RadioTable',
    'editor'         => 'Grow\\Units\\Editor',
    'date'           => 'Grow\\Units\\Date',
    'color'          => 'Grow\\Units\\Color',
    'kit'            => 'Grow\\Units\\Kit',
//    'inputUnit'      => 'Grow\\Units\\InputUnit',
//    'trbl'           => 'Grow\\Units\\TrblField',
//    'chmod'          => 'Grow\\Units\\ChmodTable',
//    'pageTree'       => 'Grow\\Units\\PageTree',
//    'pageSelector'   => 'Grow\\Units\\PageSelector',
//    'fileTree'       => 'Grow\\Units\\FileTree',
//    'fileSelector'   => 'Grow\\Units\\FileSelector',
//    'fileUpload'     => 'Grow\\Units\\Upload',
//    'tableWizard'    => 'Grow\\Units\\TableWizard',
//    'listWizard'     => 'Grow\\Units\\ListWizard',
//    'optionWizard'   => 'Grow\\Units\\OptionWizard',
//    'moduleWizard'   => 'Grow\\Units\\ModuleWizard',
//    'keyValueWizard' => 'Grow\\Units\\KeyValueWizard',
//    'imageSize'      => 'Grow\\Units\\ImageSize',
//    'metaWizard'     => 'Grow\\Units\\MetaWizard'
];


/**
 * Front end form fields
 */
$GLOBALS['TL_FFL'] = array
(
	'headline'    => 'Contao\\Forms\\FormHeadline',
	'explanation' => 'Contao\\Forms\\FormExplanation',
	'html'        => 'Contao\\Forms\\FormHtml',
	'fieldset'    => 'Contao\\Forms\\FormFieldset',
	'text'        => 'Contao\\Forms\\FormTextField',
	'password'    => 'Contao\\Forms\\FormPassword',
	'textarea'    => 'Contao\\Forms\\FormTextArea',
	'select'      => 'Contao\\Forms\\FormSelectMenu',
	'radio'       => 'Contao\\Forms\\FormRadioButton',
	'checkbox'    => 'Contao\\Forms\\FormCheckBox',
	'upload'      => 'Contao\\Forms\\FormFileUpload',
	'hidden'      => 'Contao\\Forms\\FormHidden',
	'captcha'     => 'Contao\\Forms\\FormCaptcha',
	'submit'      => 'Contao\\Forms\\FormSubmit'
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



/**
 * Register the templates
 */
\Contao\TemplateLoader::addFiles(array
(
	'analytics_google'    => 'system/plugins/core/templates/analytics',
	'analytics_piwik'     => 'system/plugins/core/templates/analytics',
	'be_changelog'        => 'system/plugins/core/templates/backend',
	'be_confirm'          => 'system/plugins/core/templates/backend',
	'be_diff'             => 'system/plugins/core/templates/backend',
	'be_error'            => 'system/plugins/core/templates/backend',
	'be_forbidden'        => 'system/plugins/core/templates/backend',
	'be_help'             => 'system/plugins/core/templates/backend',
	'be_incomplete'       => 'system/plugins/core/templates/backend',
	'be_install'          => 'system/plugins/core/templates/backend',
	'be_login'            => 'system/plugins/core/templates/backend',
	'be_main'             => 'system/plugins/core/templates/backend',
	'be_maintenance'      => 'system/plugins/core/templates/backend',
	'be_navigation'       => 'system/plugins/core/templates/backend',
	'be_no_active'        => 'system/plugins/core/templates/backend',
	'be_no_forward'       => 'system/plugins/core/templates/backend',
	'be_no_layout'        => 'system/plugins/core/templates/backend',
	'be_no_page'          => 'system/plugins/core/templates/backend',
	'be_no_root'          => 'system/plugins/core/templates/backend',
	'be_pagination'       => 'system/plugins/core/templates/backend',
	'be_password'         => 'system/plugins/core/templates/backend',
	'be_picker'           => 'system/plugins/core/templates/backend',
	'be_popup'            => 'system/plugins/core/templates/backend',
	'be_preview'          => 'system/plugins/core/templates/backend',
	'be_purge_data'       => 'system/plugins/core/templates/backend',
	'be_rebuild_index'    => 'system/plugins/core/templates/backend',
	'be_referer'          => 'system/plugins/core/templates/backend',
	'be_switch'           => 'system/plugins/core/templates/backend',
	'be_unavailable'      => 'system/plugins/core/templates/backend',

	'be_listing'          => 'system/plugins/core/templates/backend',
	'be_groups_editing'   => 'system/plugins/core/templates/backend',

	'be_welcome'          => 'system/plugins/core/templates/backend',
	'be_wildcard'         => 'system/plugins/core/templates/backend',
	'block_searchable'    => 'system/plugins/core/templates/block',
	'block_section'       => 'system/plugins/core/templates/block',
	'block_sections'      => 'system/plugins/core/templates/block',
	'block_unsearchable'  => 'system/plugins/core/templates/block',
	'be_editor_base'      => 'system/plugins/core/templates/editors',
	'be_editor_chk'       => 'system/plugins/core/templates/editors',
	'be_editor_pw'        => 'system/plugins/core/templates/editors',
	'be_editor_rdo'       => 'system/plugins/core/templates/editors',
	'ce_code'             => 'system/plugins/core/templates/elements',
	'ce_download'         => 'system/plugins/core/templates/elements',
	'ce_downloads'        => 'system/plugins/core/templates/elements',
	'ce_headline'         => 'system/plugins/core/templates/elements',
	'ce_html'             => 'system/plugins/core/templates/elements',
	'ce_hyperlink'        => 'system/plugins/core/templates/elements',
	'ce_hyperlink_image'  => 'system/plugins/core/templates/elements',
	'ce_image'            => 'system/plugins/core/templates/elements',
	'ce_list'             => 'system/plugins/core/templates/elements',
	'ce_markdown'         => 'system/plugins/core/templates/elements',
	'ce_table'            => 'system/plugins/core/templates/elements',
	'ce_text'             => 'system/plugins/core/templates/elements',
	'form'                => 'system/plugins/core/templates/forms',
	'form_captcha'        => 'system/plugins/core/templates/forms',
	'form_checkbox'       => 'system/plugins/core/templates/forms',
	'form_explanation'    => 'system/plugins/core/templates/forms',
	'form_fieldset'       => 'system/plugins/core/templates/forms',
	'form_headline'       => 'system/plugins/core/templates/forms',
	'form_hidden'         => 'system/plugins/core/templates/forms',
	'form_html'           => 'system/plugins/core/templates/forms',
	'form_message'        => 'system/plugins/core/templates/forms',
	'form_password'       => 'system/plugins/core/templates/forms',
	'form_radio'          => 'system/plugins/core/templates/forms',
	'form_row'            => 'system/plugins/core/templates/forms',
	'form_row_double'     => 'system/plugins/core/templates/forms',
	'form_select'         => 'system/plugins/core/templates/forms',
	'form_submit'         => 'system/plugins/core/templates/forms',
	'form_textarea'       => 'system/plugins/core/templates/forms',
	'form_textfield'      => 'system/plugins/core/templates/forms',
	'form_upload'         => 'system/plugins/core/templates/forms',
	'form_widget'         => 'system/plugins/core/templates/forms',
	'form_xml'            => 'system/plugins/core/templates/forms',
	'fe_page'             => 'system/plugins/core/templates/frontend',
	'gallery_default'     => 'system/plugins/core/templates/gallery',
	'mail_default'        => 'system/plugins/core/templates/mail',
	'member_default'      => 'system/plugins/core/templates/member',
	'member_grouped'      => 'system/plugins/core/templates/member',
	'mod_article'         => 'system/plugins/core/templates/modules',
	'mod_article_list'    => 'system/plugins/core/templates/modules',
	'mod_article_nav'     => 'system/plugins/core/templates/modules',
	'mod_article_plain'   => 'system/plugins/core/templates/modules',
	'mod_article_teaser'  => 'system/plugins/core/templates/modules',
	'mod_booknav'         => 'system/plugins/core/templates/modules',
	'mod_breadcrumb'      => 'system/plugins/core/templates/modules',
	'mod_change_password' => 'system/plugins/core/templates/modules',
	'mod_flash'           => 'system/plugins/core/templates/modules',
	'mod_html'            => 'system/plugins/core/templates/modules',
	'mod_login_1cl'       => 'system/plugins/core/templates/modules',
	'mod_login_2cl'       => 'system/plugins/core/templates/modules',
	'mod_logout_1cl'      => 'system/plugins/core/templates/modules',
	'mod_logout_2cl'      => 'system/plugins/core/templates/modules',
	'mod_message'         => 'system/plugins/core/templates/modules',
	'mod_navigation'      => 'system/plugins/core/templates/modules',
	'mod_password'        => 'system/plugins/core/templates/modules',
	'mod_quicklink'       => 'system/plugins/core/templates/modules',
	'mod_quicknav'        => 'system/plugins/core/templates/modules',
	'mod_search'          => 'system/plugins/core/templates/modules',
	'mod_search_advanced' => 'system/plugins/core/templates/modules',
	'mod_search_simple'   => 'system/plugins/core/templates/modules',
	'mod_sitemap'         => 'system/plugins/core/templates/modules',
	'nav_default'         => 'system/plugins/core/templates/navigation',
	'pagination'          => 'system/plugins/core/templates/pagination',
	'picture_default'     => 'system/plugins/core/templates/picture',
	'rss_default'         => 'system/plugins/core/templates/rss',
	'rss_items_only'      => 'system/plugins/core/templates/rss',
	'search_default'      => 'system/plugins/core/templates/search',
));


$GLOBALS['TL_CSS'][] = '/system/plugins/core/assets/css/core.css';

$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/Sortable.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/assets/tinymce/tinymce.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/flatpickr.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/vue.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/vue-router.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/plugins/vue-sortable.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/plugins/vue-tooltip.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/plugins/vue-sticky.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/lodash.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/axios.min.js';
$GLOBALS['TL_JAVASCRIPT'][] = '/system/plugins/core/assets/js/core.js';


$GLOBALS['EDITOR_PRESETS'] = [
    'minimal' => [
        'toolbar' => 'bold italic | alignleft aligncenter alignright'
    ],
    'simple' => [
        'toolbar' => 'bold italic | alignleft aligncenter alignright alignjustify | link unlink | removeformat code'
    ],
    'regular' => [
        'toolbar' => 'formatselect | bold italic blockquote | removeformat | alignleft aligncenter alignright alignjustify | bullist numlist | link unlink | image | visualblocks code | fullscreen'
    ]
];