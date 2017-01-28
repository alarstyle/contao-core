<?php

namespace Grow\Actions;

use Contao\BaseTemplate;
use Contao\Database;
use Contao\Date;
use Contao\Idna;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use Grow\ActionData;


abstract class AbstractAction
{

    public function __construct($table, $field)
    {
        $this->table = $table;
        $this->field = $field;
        $this->fieldData = $this->getFieldData();
        $this->attributes = $this->getAttributes();
        $this->database = Database::getInstance();

        if (empty($this->fieldData['sql'])) {
            $this->skip = true;
        }
    }

}