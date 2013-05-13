<?php

require_once ('smarty/Smarty.php');

class SmartyView
{

    protected $smarty;
    protected $template;

    public function __construct(Smarty $smarty, $template)
    {
        $this->smarty = $smarty;
        $smarty->template_dir = array(APPLICATION_TEMPLATE_DIR, COMMON_TEMPLATE_DIR);

        if (isset($template)) {
            $this->template = $template;
        }

    }

    public function render()
    {
        $this->smarty->display($this->template);
    }


}