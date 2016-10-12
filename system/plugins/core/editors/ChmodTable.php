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
 * Provide methods to handle CHMOD tables.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ChmodTable extends \Editor
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
	 * Generate the editor and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		$arrObjects = array('u'=>'cuser', 'g'=>'cgroup', 'w'=>'cworld');

		$return = '  <table id="ctrl_defaultChmod" class="tl_chmod">
    <tr>
      <th></th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editpage'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editnavigation'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['deletepage'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editarticles'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['movearticles'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['deletearticles'].'</th>
    </tr>';

		// Build rows for user, group and world
		foreach ($arrObjects as $k=>$v)
		{
			$return .= '
    <tr>
      <th scope="row">'.$GLOBALS['TL_LANG']['CHMOD'][$v].'</th>';

			// Add checkboxes
			for ($j=1; $j<=6; $j++)
			{
				$return .= '
      <td><input type="checkbox" name="'.$this->strName.'[]" value="'.specialchars($k.$j).'"'.static::optionChecked($k.$j, $this->varValue).' onfocus=""></td>';
			}

			$return .= '
    </tr>';
		}

		return $return.'
  </table>';
	}
}
