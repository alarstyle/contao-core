<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;


/**
 * Class FormHidden
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class FormHidden extends \Editor
{

	/**
	 * Submit user input
	 *
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'form_hidden';


	/**
	 * Generate the editor and return it as string
	 *
	 * @return string The editor markup
	 */
	public function generate()
	{
		return sprintf('<input type="hidden" name="%s" value="%s">',
						$this->strName,
						specialchars($this->varValue));
	}
}
