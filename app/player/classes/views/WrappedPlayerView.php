<?php

require_once('common/classes/views/SmartyView.php');
require_once('player/classes/views/header/HeaderView.php');
require_once('player/classes/views/footer/FooterView.php');

class WrappedPlayerView extends SmartyView
{
    protected $headerView;
    protected $footerView;
    protected $context;

    public function __construct(WebContext $context, $template)
    {
        /*
           * We add CSS and JavaScript to the meta view here so that
           * it is loaded before any CSS and JavaScript added by
           * subclasses
           */
        $this->context = $context;
        $this->headerView = $this->getHeaderView($context);
        $this->footerView = $this->getFooterView($context);
        parent::__construct($context->getResources()->getSmarty(), $template);
    }

    protected function getHeaderView(Context $context)
    {
        $headerView = new HeaderView($context->getResources()->getSmarty());
        $headerView->addJS('/js/jquery-1.7.1.min.js');
        $headerView->addJS('/js/jquery-ui-1.8.13.min.js');
		$headerView->addJS('/js/core.js');
		$headerView->addJS('/js/console.js');
		$headerView->addJS('/js/canvas.js');
		$headerView->addJS('/js/beacon.js');
		$headerView->addJS('/js/screen.js');
		$headerView->addJS('/js/asset.js');
		$headerView->addJS('/js/zone.js');
		$headerView->addJS('/js/layout.js');
		$headerView->addJS('/js/playlist.js');
		$headerView->addJS('/js/player.js');
		$headerView->addCSS('/css/reset.css');
		$headerView->addCSS('/css/player.css');
        return $headerView;
    }

    protected function getFooterView(Context $context)
    {
        return new FooterView($context->getResources()->getSmarty());
    }

    public function render()
    {
        $this->headerView->render();
        parent::render();
        $this->footerView->render();
    }

}