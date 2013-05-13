<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kurt
 * Date: 4/30/12
 * Time: 3:12 PM
 * To change this template use File | Settings | File Templates.
 */

require_once('player/classes/views/WrappedPlayerView.php');

class MainView extends WrappedPlayerView {

    public function __construct(WebContext $context)
    {
        parent::__construct($context, 'main/content.tpl');
    }

}