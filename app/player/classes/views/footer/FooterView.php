<?php

require_once('smarty/Smarty.php');
require_once('common/classes/views/SmartyView.php');

class FooterView extends SmartyView
{

    public function __construct(Smarty $smarty)
    {
        parent::__construct($smarty, 'footer/footer.tpl');
    }

    public function render()
    {
        parent::render();
    }

}

?>