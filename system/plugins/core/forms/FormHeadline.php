<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;

use Contao\StringUtil;

/**
 * Class FormHeadline
 *
 * @property string $text
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class FormHeadline extends \Contao\Editor
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_headline';

	/**
	 * The CSS class prefix
	 *
	 * @var string
	 */
	protected $strPrefix = 'widget widget-headline';


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

		return $this->text;
	}
}
