<?php

/**
 * Carbid for Contao Open Source CMS
 *
 * Copyright (C) 2014-2016 Alexander Stulnikov
 *
 * @link       https://github.com/alarstyle/contao-carbid
 * @license    http://opensource.org/licenses/MIT
 */

namespace Carbid;


class DC_Table_Extended extends \DC_Table
{

	protected $extendedEnabled = false;

	public function __construct($strTable, $arrModule=array())
	{
		parent::__construct($strTable, $arrModule);

		if ($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] == 4 && empty($GLOBALS['TL_DCA'][$this->strTable]['config']['ptable']))
		{
			$this->extendedEnabled = true;
		}

		// Adding request token for sorting
		if ($this->extendedEnabled && !isset($_GET['act']) && !isset($_GET['rt']) )
		{
			$this->redirect(\Environment::get('request') . '&id=1&rt=' . REQUEST_TOKEN);
		}

		if (\Input::get('act') == 'cut' && !strlen(\Input::get('pid')))
		{
			\Input::setGet('pid', '0');
		}
	}

	/**
 	 * Show header of the parent table and list all records of the current table
	 *
	 * @return string
	 */
	protected function parentView()
	{
		if (!$this->extendedEnabled)
		{
			return parent::parentView();
		}

		$blnClipboard = false;
		$arrClipboard = $this->Session->get('CLIPBOARD');
		$table = $this->strTable;
		$blnHasSorting = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields'][0] == 'sorting';
		$blnMultiboard = false;

		// Check clipboard
		if (!empty($arrClipboard[$table]))
		{
			$blnClipboard = true;
			$arrClipboard = $arrClipboard[$table];

			if (is_array($arrClipboard['id']))
			{
				$blnMultiboard = true;
			}
		}

		// Load the fonts to display the paste hint
		\Config::set('loadGoogleFonts', $blnClipboard);

		$return = '
<div id="tl_buttons">' . (\Input::get('nb') ? '&nbsp;' : ($this->ptable ? '
<a href="'.$this->getReferer(true, $this->ptable).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b" onclick="Backend.getScrollOffset()">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>' : (isset($GLOBALS['TL_DCA'][$this->strTable]['config']['backlink']) ? '
<a href="contao/main.php?'.$GLOBALS['TL_DCA'][$this->strTable]['config']['backlink'].'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b" onclick="Backend.getScrollOffset()">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>' : ''))) . ' ' . ((\Input::get('act') != 'select' && !$blnClipboard && !$GLOBALS['TL_DCA'][$this->strTable]['config']['closed'] && !$GLOBALS['TL_DCA'][$this->strTable]['config']['notCreatable']) ? '
<a href="'.$this->addToUrl('act=create&amp;mode=2&amp;pid=0').'" class="header_new" title="'.specialchars($GLOBALS['TL_LANG'][$this->strTable]['new'][1]).'" accesskey="n" onclick="Backend.getScrollOffset()">'.$GLOBALS['TL_LANG'][$this->strTable]['new'][0].'</a> ' : '') . ($blnClipboard ? '
<a href="'.$this->addToUrl('clipboard=1').'" class="header_clipboard" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['clearClipboard']).'" accesskey="x">'.$GLOBALS['TL_LANG']['MSC']['clearClipboard'].'</a> ' : $this->generateGlobalButtons()) . '
</div>' . \Message::generate(true);

		$return .= ((\Input::get('act') == 'select') ? '

<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_select" class="tl_form'.((\Input::get('act') == 'select') ? ' unselectable' : '').'" method="post" novalidate>
<div class="tl_formbody">
<input type="hidden" name="FORM_SUBMIT" value="tl_select">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">' : '').($blnClipboard ? '

<div id="paste_hint">
  <p>'.$GLOBALS['TL_LANG']['MSC']['selectNewPosition'].'</p>
</div>' : '').'

<div class="tl_listing_container parent_view">';

		// List all records of the child table
		if (!\Input::get('act') || \Input::get('act') == 'paste' || \Input::get('act') == 'select' || true)
		{
			$this->import('BackendUser', 'User');

			$orderBy = array();
			$firstOrderBy = array();

			// Add all records of the current table
			$query = "SELECT * FROM " . $this->strTable;

			if (is_array($this->orderBy) && strlen($this->orderBy[0]))
			{
				$orderBy = $this->orderBy;
				$firstOrderBy = preg_replace('/\s+.*$/', '', $orderBy[0]);

				// Order by the foreign key
				if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['foreignKey']))
				{
					$key = explode('.', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['foreignKey'], 2);
					$query = "SELECT *, (SELECT ". $key[1] ." FROM ". $key[0] ." WHERE ". $this->strTable .".". $firstOrderBy ."=". $key[0] .".id) AS foreignKey FROM " . $this->strTable;
					$orderBy[0] = 'foreignKey';
				}
			}
			elseif (is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields']))
			{
				$orderBy = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields'];
				$firstOrderBy = preg_replace('/\s+.*$/', '', $orderBy[0]);
			}

			$arrProcedure = $this->procedure;
			$arrValues = $this->values;

			// Support empty ptable fields (backwards compatibility)
			if ($GLOBALS['TL_DCA'][$this->strTable]['config']['dynamicPtable'])
			{
				$arrProcedure[] = ($this->ptable == 'tl_article') ? "(ptable=? OR ptable='')" : "ptable=?";
				$arrValues[] = $this->ptable;
			}

			// WHERE
			if (!empty($arrProcedure))
			{
				$query .= " WHERE " . implode(' AND ', $arrProcedure);
			}
			if (!empty($this->root) && is_array($this->root))
			{
				$query .= (!empty($arrProcedure) ? " AND " : " WHERE ") . "id IN(" . implode(',', array_map('intval', $this->root)) . ")";
			}

			// ORDER BY
			if (!empty($orderBy) && is_array($orderBy))
			{
				$query .= " ORDER BY " . implode(', ', $orderBy);
			}

			$objOrderByStmt = $this->Database->prepare($query);

			// LIMIT
			if (strlen($this->limit))
			{
				$arrLimit = explode(',', $this->limit);
				$objOrderByStmt->limit($arrLimit[1], $arrLimit[0]);
			}

			$objOrderBy = $objOrderByStmt->execute($arrValues);

			if ($objOrderBy->numRows < 1)
			{
				return $return . '
<p class="tl_empty_parent_view">'.$GLOBALS['TL_LANG']['MSC']['noResult'].'</p>

</div>';
			}

			// Call the child_record_callback
			if (is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback']) || is_callable($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback']))
			{
				$strGroup = '';
				$blnIndent = false;
				$intWrapLevel = 0;
				$row = $objOrderBy->fetchAllAssoc();

				// Make items sortable
				if ($blnHasSorting)
				{
					$return .= '

<ul id="ul_' . CURRENT_ID . '">';
				}

				for ($i=0, $c=count($row); $i<$c; $i++)
				{
					$this->current[] = $row[$i]['id'];
					$imagePasteAfter = \Image::getHtml('pasteafter.gif', sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id']));
					$imagePasteNew = \Image::getHtml('new.gif', sprintf($GLOBALS['TL_LANG'][$this->strTable]['pastenew'][1], $row[$i]['id']));

					// Decrypt encrypted value
					foreach ($row[$i] as $k=>$v)
					{
						if ($GLOBALS['TL_DCA'][$table]['fields'][$k]['eval']['encrypt'])
						{
							$row[$i][$k] = \Encryption::decrypt(deserialize($v));
						}
					}

					// Make items sortable
					if ($blnHasSorting)
					{
						$return .= '
<li id="li_' . $row[$i]['id'] . '">';
					}

					// Add the group header
					if (!$GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['disableGrouping'] && $firstOrderBy != 'sorting')
					{
						$sortingMode = (count($orderBy) == 1 && $firstOrderBy == $orderBy[0] && $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['flag'] != '' && $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['flag'] == '') ? $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['flag'] : $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['flag'];
						$remoteNew = $this->formatCurrentValue($firstOrderBy, $row[$i][$firstOrderBy], $sortingMode);
						$group = $this->formatGroupHeader($firstOrderBy, $remoteNew, $sortingMode, $row);

						if ($group != $strGroup)
						{
							$return .= "\n\n" . '<div class="tl_content_header">'.$group.'</div>';
							$strGroup = $group;
						}
					}

					$blnWrapperStart = in_array($row[$i]['type'], $GLOBALS['TL_WRAPPERS']['start']);
					$blnWrapperSeparator = in_array($row[$i]['type'], $GLOBALS['TL_WRAPPERS']['separator']);
					$blnWrapperStop = in_array($row[$i]['type'], $GLOBALS['TL_WRAPPERS']['stop']);

					// Closing wrappers
					if ($blnWrapperStop)
					{
						if (--$intWrapLevel < 1)
						{
							$blnIndent = false;
						}
					}

					$return .= '

<div class="tl_content'.($blnWrapperStart ? ' wrapper_start' : '').($blnWrapperSeparator ? ' wrapper_separator' : '').($blnWrapperStop ? ' wrapper_stop' : '').($blnIndent ? ' indent indent_'.$intWrapLevel : '').(($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_class'] != '') ? ' ' . $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_class'] : '').(($i%2 == 0) ? ' even' : ' odd').' click2edit toggle_select" onmouseover="Theme.hoverDiv(this,1)" onmouseout="Theme.hoverDiv(this,0)">
<div class="tl_content_right">';

					// Opening wrappers
					if ($blnWrapperStart)
					{
						if (++$intWrapLevel > 0)
						{
							$blnIndent = true;
						}
					}

					// Edit multiple
					if (\Input::get('act') == 'select')
					{
						$return .= '<input type="checkbox" name="IDS[]" id="ids_'.$row[$i]['id'].'" class="tl_tree_checkbox" value="'.$row[$i]['id'].'">';
					}

					// Regular buttons
					else
					{
						$return .= $this->generateButtons($row[$i], $this->strTable, $this->root, false, null, $row[($i-1)]['id'], $row[($i+1)]['id']);

						// Sortable table
						if ($blnHasSorting)
						{
							// Create new button
							if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['closed'] && !$GLOBALS['TL_DCA'][$this->strTable]['config']['notCreatable'])
							{
								$return .= ' <a href="'.$this->addToUrl('act=create&amp;mode=1&amp;pid='.$row[$i]['id'].'&amp;id='.$objParent->id).'" title="'.specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pastenew'][1], $row[$i]['id'])).'">'.$imagePasteNew.'</a>';
							}

							// Prevent circular references
							if ($blnClipboard && $arrClipboard['mode'] == 'cut' && $row[$i]['id'] == $arrClipboard['id'] || $blnMultiboard && $arrClipboard['mode'] == 'cutAll' && in_array($row[$i]['id'], $arrClipboard['id']))
							{
								$return .= ' ' . \Image::getHtml('pasteafter_.gif');
							}

							// Copy/move multiple
							elseif ($blnMultiboard)
							{
								$return .= ' <a href="'.$this->addToUrl('act='.$arrClipboard['mode'].'&amp;mode=1&amp;pid='.$row[$i]['id']).'" title="'.specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id'])).'" onclick="Backend.getScrollOffset()">'.$imagePasteAfter.'</a>';
							}

							// Paste buttons
							elseif ($blnClipboard)
							{
								$return .= ' <a href="'.$this->addToUrl('act='.$arrClipboard['mode'].'&amp;mode=1&amp;pid='.$row[$i]['id'].'&amp;id='.$arrClipboard['id']).'" title="'.specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id'])).'" onclick="Backend.getScrollOffset()">'.$imagePasteAfter.'</a>';
							}

							// Drag handle
							if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notSortable'])
							{
								$return .= ' ' . \Image::getHtml('drag.gif', '', 'class="drag-handle" title="' . sprintf($GLOBALS['TL_LANG'][$this->strTable]['cut'][1], $row[$i]['id']) . '"');
							}
						}
					}

					if (is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback']))
					{
						$strClass = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback'][0];
						$strMethod = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback'][1];

						$this->import($strClass);
						$return .= '</div>'.$this->$strClass->$strMethod($row[$i]).'</div>';
					}
					elseif (is_callable($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback']))
					{
						$return .= '</div>'.$GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback']($row[$i]).'</div>';
					}

					// Make items sortable
					if ($blnHasSorting)
					{
						$return .= '</li>';
					}
				}
			}
		}

		// Make items sortable
		if ($blnHasSorting && !$GLOBALS['TL_DCA'][$this->strTable]['config']['notSortable'] && \Input::get('act') != 'select')
		{
			$return .= '
</ul>

<script>
  Backend.makeParentViewSortable("ul_' . CURRENT_ID . '");
</script>';
		}

		$return .= '

</div>';

		// Close form
		if (\Input::get('act') == 'select')
		{
			// Submit buttons
			$arrButtons = array();

			if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notDeletable'])
			{
				$arrButtons['delete'] = '<input type="submit" name="delete" id="delete" class="tl_submit" accesskey="d" onclick="return confirm(\''.$GLOBALS['TL_LANG']['MSC']['delAllConfirm'].'\')" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['deleteSelected']).'">';
			}

			if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notSortable'])
			{
				$arrButtons['cut'] = '<input type="submit" name="cut" id="cut" class="tl_submit" accesskey="x" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['moveSelected']).'">';
			}

			if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notCopyable'])
			{
				$arrButtons['copy'] = '<input type="submit" name="copy" id="copy" class="tl_submit" accesskey="c" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['copySelected']).'">';
			}

			if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notEditable'])
			{
				$arrButtons['override'] = '<input type="submit" name="override" id="override" class="tl_submit" accesskey="v" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['overrideSelected']).'">';
				$arrButtons['edit'] = '<input type="submit" name="edit" id="edit" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['editSelected']).'">';
			}

			// Call the buttons_callback (see #4691)
			if (is_array($GLOBALS['TL_DCA'][$this->strTable]['select']['buttons_callback']))
			{
				foreach ($GLOBALS['TL_DCA'][$this->strTable]['select']['buttons_callback'] as $callback)
				{
					if (is_array($callback))
					{
						$this->import($callback[0]);
						$arrButtons = $this->{$callback[0]}->{$callback[1]}($arrButtons, $this);
					}
					elseif (is_callable($callback))
					{
						$arrButtons = $callback($arrButtons, $this);
					}
				}
			}

			$return .= '

<div class="tl_formbody_submit" style="text-align:right">

<div class="tl_submit_container">
  ' . implode(' ', $arrButtons) . '
</div>

</div>
</div>
</form>';
		}

		return $return;
	}


	protected function getNewPosition($mode, $pid=null, $insertInto=false)
	{
		if (!$this->extendedEnabled) {
			parent::getNewPosition($mode, $pid, $insertInto);
			return;
		}

		if ($pid === null && $this->intId && $mode == 'copy')
		{
			$pid = $this->intId;
		}

		// PID is set (insert after record)
		if (is_numeric($pid))
		{
			$newSorting = 0;
			$prevSorting = 0;

			// Find previous sorting
			if ($pid > 0 && !$insertInto) {
				$objPrevSorting = $this->Database->prepare("SELECT id, sorting FROM " . $this->strTable . " WHERE id=?")
					->limit(1)
					->execute($pid);
				if ($objPrevSorting->numRows)
				{
					$prevSorting = $objPrevSorting->sorting;
				}
			}

			// Find next sorting
			$objNextSorting = $this->Database->prepare("SELECT MIN(sorting) AS sorting FROM " . $this->strTable . " WHERE sorting>?")
				->execute($prevSorting);

			if ($objNextSorting->sorting !== null)
			{
				$nextSorting = $objNextSorting->sorting;

				// Resort if the new sorting value is no integer or bigger than a MySQL integer
				if ((($prevSorting + $nextSorting) % 2) != 0 || $nextSorting >= 4294967295)
				{
					$count = 1;

					$objNewSorting = $this->Database->prepare("SELECT id, sorting FROM " . $this->strTable . " ORDER BY sorting")
						->execute();

					while ($objNewSorting->next())
					{
						$this->Database->prepare("UPDATE " . $this->strTable . " SET sorting=? WHERE id=?")
							->execute(($count++ * 128), $objNewSorting->id);

						if ($objNewSorting->sorting == $prevSorting)
						{
							$newSorting = ($count++ * 128);
						}
					}
				}

				// Else new sorting = (current sorting + next sorting) / 2
				else $newSorting = (($prevSorting + $nextSorting) / 2);
			}
			else
			{
				$newSorting = $prevSorting + 128;
			}

			// Set new sorting
			$this->set['sorting'] = intval($newSorting);
		}

	}

}
