<?php
require_once 'config/config.php';

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($controllerName) {
    case 'auth':
        require_once 'controller/AuthController.php';
        $auth = new AuthController($db);
        if ($action == 'login') {
            $auth->login();
        } elseif ($action == 'register') {
            $auth->register();
        } elseif ($action == 'logout') {
            $auth->logout();
        }
        break;
    case 'transfer':
        require_once 'controller/TransferController.php';
        $transfer = new TransferController($db);
        $transfer->index();
        break;
    case 'profile':
        require_once 'controller/ProfileController.php';
        $profile = new ProfileController($db);
        if ($action == 'update') {
            $profile->update();
        } elseif ($action == 'view') {
            $profile->view();
        } elseif ($action == 'list') {
            $profile->list();
        } else {
            $profile->index();
        }
        break;
    case 'search':
        require_once 'controller/SearchController.php';
        $search = new SearchController($db);
        $search->index();
        break;
    case 'transaction':
        require_once 'controller/TransactionController.php';
        $transaction = new TransactionController($db);
        $transaction->index();
        break;
    default:
        require_once 'controller/HomeController.php';
        $home = new HomeController($db);
        $home->index();
        break;

}
?>
