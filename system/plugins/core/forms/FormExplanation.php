<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;

use Contao\Config;
use Contao\StringUtil;

/**
 * Class FormExplanation
 *
 * @property string $text
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class FormExplanation extends \Contao\Editor
{

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'form_explanation';

	/**
	 * The CSS class prefix
	 *
	 * @var string
	 */
	protected $strPrefix = 'widget widget-explanation';


	/**
	 * Do not validate
	 */
	public function validate()
	{
		return;
	}


	/**
	 * Generate the editor and return it as string
	 *
	 * @return string The editor markup
	 */
	public function generate()
	{
		$this->text = StringUtil::toHtml5($this->text);

		// Add the static files URL to images
		if (TL_FILES_URL != '')
		{
			$path = Config::get('uploadPath') . '/';
			$this->text = str_replace(' src="' . $path, ' src="' . TL_FILES_URL . $path, $this->text);
		}

		return StringUtil::encodeEmail($this->text);
	}
}
