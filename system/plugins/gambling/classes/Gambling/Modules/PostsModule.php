<?php

namespace Gambling\Modules;

use Contao\Config;
use Contao\Modules\AbstractModule;

class PostsModule extends AbstractModule
{

    protected $strTemplate = 'mod_posts';


    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            return 'Posts';
        }

        // Show the news reader if an item has been selected
        if ($this->news_readerModule > 0 && (isset($_GET['items']) || (Config::get('useAutoItem') && isset($_GET['auto_item']))))
        {
            return $this->getFrontendModule($this->news_readerModule, $this->strColumn);
        }

        // Hide the module if no period has been selected
        if ($this->news_jumpToCurrent == 'hide_module' && !isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day']))
        {
            return '';
        }

        return parent::generate();
    }


    protected function compile()
    {

    }

}