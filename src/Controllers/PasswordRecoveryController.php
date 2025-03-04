<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Controllers\BaseController;
use Src\Services\AuthService;
use Src\Services\UserService;
use Src\Entities\User;

Class PasswordRecoveryController extends BaseController
{
    
    public static function index(){
        $Security = new AuthService();
        $userServices = new UserService();
        self::init();
        $alert = false ;
        $ajout = false ;

        if (!empty($_POST['email'])) {
             $body = [
                'email' => $_POST['email'] 
            ];
  
            $user =  $userServices->recoveryPassword( $body );
            if (!empty($user->response)) {
              $alert = 'Un email de récupération à été envoyé a '. $_POST['email'] ;
              $ajout = "Veuillez vérifier votre boîte de réception et suivre les instructions dans dans l'email pour réinitialiser votre mot de passe.
               Si vous ne trouvez pas l'email, vérififiez votre dossier de spam ou courrier indésirable!" ;
            }
           
        }
           
        return self::$twig->render(
            'passwordRecovery.html.twig',[
                'alert' => $alert, 
                'ajout' => $ajout
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