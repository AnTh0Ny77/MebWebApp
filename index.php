<?php
require "vendor/autoload.php";
use Src\Controllers\IndexController as index;
use Src\Controllers\HomeController as home;
use Src\Controllers\QrController as qr;
use Src\Controllers\PaymentController as pay;
use Src\Controllers\StatController as stat;
use Src\Controllers\PasswordChangeController as pass;
use Src\Controllers\UserController as user;
use Src\Controllers\UserFormsController as forms;

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
    case '/MebWebApp/':
        echo index::index();
        break;

    case '/MebWebApp/login':
        echo index::index();
        break;

    case '/MebWebApp/home':
        echo home::index();
        break;

    case '/MebWebApp/qr':
        echo qr::index();
        break;

    case '/MebWebApp/exc'.$getData:
        echo pay::index();
        break;

    case '/MebWebApp/amount':
        echo pay::amount();
        break;
    
    case '/MebWebApp/stat':
        echo stat::index();
        break;

    case '/MebWebApp/logout':
        echo index::logout();
        break;

    case '/MebWebApp/pass':
        echo pass::index();
        break;

    case '/MebWebApp/user':
        echo user::index();
        break;

    case '/MebWebApp/formsUser'.$getData:
        echo forms::index();
        break;
    
    default:
        header('HTTP/1.0 404 not found');
        echo  index::error404();
        break;
}

