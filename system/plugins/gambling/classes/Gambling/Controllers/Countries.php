<?php

namespace Gambling\Controllers;

use Contao\Input;
use Grow\ActionData;
use Grow\Controllers\GroupsEditing;

class Countries extends GroupsEditing
{

    public function ajaxSaveGroup()
    {
        $id = Input::post('id');

        parent::ajaxSaveGroup();

//        if ($id === 'new' && !$this->groupOrganizer->hasErrors()) {
//            $countryId = ActionData::getData('newId');
//            $connection = \Grow\Database::getConnection();
//            $categories = $connection->selectQuery()
//                ->table('tl_casino_category')
//                ->fields('id')
//                ->execute()->asArray();
//            if (empty($categories)) return;
//            foreach ($categories as $category) {
//                $connection->insertQuery()
//                    ->table('tl_casino_category_data')
//                    ->data([
//                        'pid' => $category->id,
//                        'country' => $countryId,
//                        'sorting' => 0
//                    ])
//                    ->execute();
//            }
//        }
    }

}