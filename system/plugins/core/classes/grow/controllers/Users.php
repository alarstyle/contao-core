<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 10.11.16
 * Time: 21:27
 */

namespace Grow\Controllers;

use Contao\BackendTemplate;

class Users extends \Contao\Controllers\BackendMain
{

    protected $config;


    public function __construct($config = null)
    {
        $this->config = $config;
        parent::__construct();
    }


    protected function generateMainSection()
    {
        $objTemplate = new BackendTemplate('be_users');

        if ($this->config['group']) {
            $objTemplate->group = $this->generateGroup();
        }

        if ($this->config['list']) {
            $objTemplate->list = $this->generateList();
        }

        $this->Template->main = $objTemplate->parse();
    }


    protected function generateGroup() {
        return '';
    }


    protected function generateList() {

        $query = "SELECT * FROM " . $this->config['list']['table'];

        $objRowStmt = $this->Database->prepare($query);

        $objRowStmt->limit(20, 0);

        $objRow = $objRowStmt->execute($this->values);

        if ($objRow->numRows < 1) {
            return 'No data';
        }

        $result = $objRow->fetchAllAssoc();

        return '';
    }

}