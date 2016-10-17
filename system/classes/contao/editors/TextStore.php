<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\Editors;


/**
 * A TextStore field is used to enter data only. It will not show the
 * currently stored value (useful e.g. to store passwords).
 *
 * @property integer $maxlength
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class TextStore extends \Contao\Editor
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_editor_base';


	/**
	 * Add specific attributes
	 *
	 * @param string $strKey
	 * @param mixed  $varValue
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'maxlength':
				if ($varValue > 0)
				{
					$this->arrAttributes['maxlength'] = $varValue;
				}
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Ignore the field if nothing has been entered
	 *
	 * @param mixed $varInput
	 *
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		if ($varInput == '*****')
		{
			$this->blnSubmitInput = false;

			return true;
		}

		return parent::validator($varInput);
	}


	/**
	 * Generate the editor and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		return sprintf('<input type="password" name="%s" id="ctrl_%s" class="tl_text%s" value="%s"%s onfocus="">%s',
						$this->strName,
						$this->strId,
						(($this->strClass != '') ? ' ' . $this->strClass : ''),
						(($this->varValue != '') ? '*****' : ''),
						$this->getAttributes(),
						$this->wizard);
	}
}
