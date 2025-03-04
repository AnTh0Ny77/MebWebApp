<?php
require "vendor/autoload.php";
use Src\Controllers\IndexController as index;
use Src\Controllers\HomeController as home;
use Src\Controllers\QrController as qr;
use Src\Controllers\Qr2Controller as qr2;
use Src\Controllers\PaymentController as pay;
use Src\Controllers\StatController as stat;
use Src\Controllers\PasswordChangeController as pass;
use Src\Controllers\UserController as user;
use Src\Controllers\UserFormsController as forms;
use Src\Controllers\DeleteUserController as delete;
use Src\Controllers\PasswordRecoveryController as reco;

session_start();
$request = $_SERVER['REQUEST_URI'];
$getRequest = explode('?' ,$request, 2);
$getData = null;

if (isset($get_request[1])) 
	$getData = '?' . $getRequest[1];

if (!isset($getRequest[1])) 
	$getData = '';

$globalRequest = $getRequest[0] . $getData; 

switch ($globalRequest){
    case '/':
        echo index::index();
        break;

    case '/login':
        echo index::index();
        break;

    case '/home':
        echo home::index();
        break;

    case '/qr':
        echo qr2::index();
        break;

     case '/qr2'.$getData:
        echo qr2::index();
        break;

    case '/exc'.$getData:
        echo pay::index();
        break;

    case '/suppression-donnees'.$getData:
        echo delete::index();
        break;

    case '/amount':
        echo pay::amount();
        break;
    
    case '/stat':
        echo stat::index();
        break;

    case '/logout':
        echo index::logout();
        break;

    case '/pass':
        echo pass::index();
        break;

    case '/user':
        echo user::index();
        break;

    case '/formsUser'.$getData:
        echo forms::index();
        break;

    case '/passwordRecovery'.$getData:
        echo reco::index();
        break;
    
    default:
        header('HTTP/1.0 404 not found');
        echo  index::error404();
        break;
}

