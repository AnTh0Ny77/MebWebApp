<?php
namespace Src\Services;

use DateTime;
use GuzzleHttp\Client;
use Src\Entities\User;
use Src\Services\MappingServices;
use GuzzleHttp\Exception\ClientException;
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

    public function getRankCover($user){
        $authService = new AuthService();
       
        if (!$user instanceof User)
            return $this->returnError( 'Reconnexion requise');

        try {
            $user = $this->Client->get('/api/rank/'.$user->getRank()->id.'/cover', ['headers' => $authService->makeHeadersUser($user)]);
        } catch(ClientException $exeption) {$user = $exeption->getResponse();}
        
        $response = $user->getBody()->read(32768);
        
        return $response;
    }

    public function getAdminData($user){
       
        $mappingService = new MappingServices();
        $authService = new AuthService();
       
        if (!$user instanceof User)
            return $this->returnError( 'Reconnexion requise');

        try {
            $user = $this->Client->get('/api/user/'.$user->getId().'/client', ['headers' => $authService->makeHeadersUser($user)]);
        } catch(ClientException $exeption) {$user = $exeption->getResponse();}
        
        $response = json_decode($user->getBody()->read(32768));
       
        return $response;
    }

    public function handleStats($user){
        
        if (!empty($user->getStats())) {
                $count_qr = count($user->getStats()->qr_generate);
                $count_scan = count($user->getStats()->Unlock_games);
                $count_encours = 0;
                $count_expire = 0;
                foreach($user->getStats()->Unlock_games as  $value){
                    if (!empty($value->date)) {
                        $game_date = new DateTime($value->date);
                        $now = new DateTime('now');
                        if ($game_date > $now) {
                            $count_encours ++;
                        }else { $count_expire ++ ;}
                    }
                }
            $stats = [
                "qr_total" =>  $count_qr, 
                "scan_total" => $count_scan,
                "encour_total" => $count_encours , 
                "expire_total" => $count_expire
            ] ;

            return $stats;
        }
        return null;
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