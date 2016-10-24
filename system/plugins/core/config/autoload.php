<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Contao',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(

	// Library
	'Contao\ClassLoader'               => 'system/plugins/core/library/Contao/ClassLoader.php',
	'Contao\DcaExtractor'              => 'system/plugins/core/library/Contao/DcaExtractor.php',
	'Contao\File'                      => 'system/plugins/core/library/Contao/File.php',
	'Contao\Files'                     => 'system/plugins/core/library/Contao/Files.php',
	'Contao\Folder'                    => 'system/plugins/core/library/Contao/Folder.php',
	'Contao\Input'                     => 'system/plugins/core/library/Contao/Input.php',
));


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
