<?php

require_once('smarty/Smarty.php');
require_once('common/classes/views/SmartyView.php');

class HeaderView extends SmartyView
{
    private $title = '';

    private $css = array();
    private $js = array();
    private $inlineJS = array();

    private $metaProperties = array();

    public function __construct(Smarty $smarty)
    {
        parent::__construct($smarty, 'header/header.tpl');
    }

    public function setTitle($title)
    {
        if (!$this->title)
            $this->title = $title;
    }

    public function addCSS($path)
    {
        array_push($this->css, $path . '?rand=' . time());
    }

    public function addJS($path)
    {
        array_push($this->js, $path . '?rand=' . time());
    }

    public function addInlineJS($script)
    {
        array_push($this->inlineJS, $script);
    }

    public function setMetaProperty($propertyName, $propertyValue)
    {
        $this->metaProperties[$propertyName] = $propertyValue;
    }

    public function getMetaProperty($propertyName)
    {
        if (isset($this->metaProperties[$propertyName]))
            return $this->metaProperties[$propertyName];
        else
            return null;
    }

    public function render()
    {

        $this->smarty->assign('title', $this->title);

        $this->smarty->assign('css', $this->css);
        $this->smarty->assign('js', $this->js);
        $this->smarty->assign('inlineJS', $this->inlineJS);

        // Extra Meta Properties
        $this->smarty->assign('meta_properties', $this->metaProperties);

        parent::render();

    }
}