<?php

namespace Gambling;

use Grow\Organizer;

class ContentOrganizer extends Organizer
{


    public function __construct()
    {
        parent::__construct('tl_content');
    }


    /**
     * @param $id String Page ID
     * @return array
     * @throws \Exception
     */
    public function load($id)
    {
        $objRow = $this->database->prepare("SELECT * FROM tl_content WHERE pid = ? AND ptable='tl_page' ORDER BY sorting")
            ->execute($id);

        $fetchedRows = $objRow->fetchAllAssoc();

        $fields = [];

        foreach ($fetchedRows as $row) {
            $unitsData = $this->getUnitsData($row);
            if (empty($unitsData)) continue;
            foreach ($unitsData as $filedName=>$unitData) {
                $fields[$row['id'] . '::' . $filedName] = $unitData;
            }

        }

        return $fields;
    }


    public function getUnitsData($row)
    {
        $tableData = $GLOBALS['TL_DCA'][$this->table];

        $palette = $tableData['palettes'][$row['type'].'New'] ?: $tableData['palettes'][$row['type']];

        if (!$palette) return null;

        $fieldsNames = $this->getFieldsNamesFromPalette($palette);
        $fields = $this->getUnitsDataForFields($row, $fieldsNames);

        return $fields;
    }

}