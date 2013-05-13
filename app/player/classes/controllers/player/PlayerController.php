<?

require_once ('common/classes/controllers/BaseViewController.php');

class PlayerController extends BaseViewController
{

    public function _preprocess($action)
    {
    }

    public function index()
    {
        require_once('player/classes/views/main/MainView.php');
        $view = new MainView($this->context);
        $view->render();
    }

}

?>