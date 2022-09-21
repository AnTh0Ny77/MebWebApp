<?php
namespace Src\Services;

use Src\Entities\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Src\Services\MappingServices;
require  '././vendor/autoload.php';

class UserService {

    public function __construct(){
        $this->Config = file_get_contents('globalConfig.json');
        $this->Config = json_decode($this->Config);
        $this->Client =  new Client(['base_uri' => $this->Config->AuthService->url ,'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
    }

    public function returnError( $message){
        return [
            'message' => $message
       ];
    }

    public function handleRole($user){

        if (!$user instanceof User) 
            return false;
        
        $count = 0 ;
        foreach ($user->getRoles() as $key => $value) {
            if ( $value == 'ROLE_ADMIN' or  $value == 'ROLE_CLIENT') {
                $count++;
            }
        }
        if ($count == 0 ) 
            return false;
        
        return $user;
    }

    public function getAdminData($user){
       
        $mappingService = new MappingServices();
        $authService = new AuthService();
       
        if (!$user instanceof User)
            return $this->returnError( 'Reconnexion requise');

        try {
            $user = $this->Client->get('/api/user/'.$user->getId().'/client', ['headers' => $authService->makeHeadersUser($user)]);
        } catch(ClientException $exeption) {$user = $exeption->getResponse();}


        return json_decode($user->getBody()->read(1024));
    }

    public function handleCoverPath(){

    }

    public function autoRefresh($user){
        $mappingService = new MappingServices();
        $authService = new AuthService();

       
        if (!$user instanceof User)
            return $this->returnError( 'Reconnexion requise');

        if (empty($user->getRefresh_token()))
            return $this->returnError( 'Reconnexion requise pas de token');

       
        $refresh = $authService->refresh($user->getRefresh_token());
        
       

        if (empty($refresh)) 
            return $this->returnError('Reconnexion requise, jeton expiré');

        
        $user->setToken($refresh->token);

        try {
            $user = $this->Client->get('/api/user/me', ['headers' => $authService->makeHeaders($refresh)]);
        } catch(ClientException $exeption) {
            $user = $exeption->getResponse();
        }

        
        
        if (!intval($user->getStatusCode()) == 200) 
            return $this->returnError('Un problème est survenu dans la récupération des données');

        
        $user = json_decode($user->getBody()->read(1024));
        $user =  $mappingService->map($user , User::class);
        $user->setToken($refresh->token);
        $user->setRefresh_token($refresh->refresh_token);
        if (!$user instanceof User) 
            return $this->returnError('Un problème est survenu merci de contacter MyExploreBag');

        // $user = $this->handleRole($user);

        // if (!$user instanceof User) 
        //     return $this->returnError(' l utilisation de l application web MyExploreLab est reservée au administrateur, rdv sur : ');
        
        
        return $user;
    }
    
}