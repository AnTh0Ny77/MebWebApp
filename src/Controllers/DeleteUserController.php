<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Services\UserService;
use Src\Entities\User;

Class DeleteUserController extends BaseController
{

    public static function path(){
        return 'suppression-donnees';
    }
    
    public static function index(){
        $Security = new AuthService();
        $userServices = new UserService();
        self::init();
        $alert = false ;
        $token = false ;
        $id = false ;
        
        
        if (!empty($_GET['token'])) $token = $_GET['token'];

        if (!empty($_GET['id'])) $id = $_GET['id'];

        

        if (!empty($_POST['email']) and !empty($_POST['token'])) {
           
             $body = [
                'token' => $_POST['token'], 
                'email' => $_POST['email'] 
            ];
  
            
            $response = $userServices->deleteUser($body);
           
            $alert = $response->message;
        }


        return self::$twig->render(
            'deleteuser.html.twig',[
                'alert' => $alert, 
                'token' => $token , 
                'id' => $id
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