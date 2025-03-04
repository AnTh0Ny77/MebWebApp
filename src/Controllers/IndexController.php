<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Entities\User;

Class IndexController extends BaseController
{
    
    public static function index(){
        $Security = new AuthService();
        self::init();
        $alert = false ;

        

        if (!empty($_POST['username']) && !empty($_POST['password'])) 
            $user =  $Security->login($_POST['username'] , $_POST['password'] );

        if (!empty($user ) and !$user instanceof User) 
            $alert = [ "message" => 'identifiants invalides' , 'username' =>  $_POST['username'] , 'password' =>   $_POST['password']];

        if (!empty($user) and $user instanceof User) 
            $_SESSION['user'] =  $user;

        if (isset($_SESSION['user']) and $_SESSION['user'] instanceof User) 
            return header('location: home');

        if (!empty($_SESSION['alert'])) {
                $alert['message'] =  $_SESSION['alert']['message'];
                $_SESSION['alert'] = '';
        }
        
        return self::$twig->render(
            'login.html.twig',[
                'alert' => $alert
            ]
        );
        
    }

    public static function logout(){

        $_SESSION['user'] = '';
        return header('location: login');
    }


    public static function error404(){
        self::init();
    }
   
}