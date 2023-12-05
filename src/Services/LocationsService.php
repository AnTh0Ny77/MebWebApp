<?php
namespace Src\Services;
use Src\Entities\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Src\Services\MappingServices;
require  '././vendor/autoload.php';


class LocationsService { 
    public function __construct(){
        
        $this->Config = file_get_contents('globalConfig.json');
        $this->Config = json_decode($this->Config);
        $this->AuthService = new AuthService();
        $this->Client =  new Client(['base_uri' => $this->Config->AuthService->url ,'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
        
    }

    public function getLocations($user){
       
        try {
             $response = $this->Client->get('/api/clientLocation?order[booleanColumn]=desc&order[id]=asc',
             [ ]);
             
        } catch(ClientException $exeption) { $response = $exeption->getResponse();}
     
        
       return json_decode($response->getBody()->read(1024458));  
    }

    public function postLocation( $user , $location){

    try {
        $user = $this->Client->post('/api/clientLocation', [  'headers' => $this->AuthService->makeHeadersUser($user)  ,
        'json' => $location]);
    } catch(ClientException $exeption) {$user = $exeption->getResponse();}

     return  json_decode($user->getBody()->read(1024));  
  
    }


    public function defaultLocation( $user , $location){

    try {
        $user = $this->Client->post('/api/clientLocation', [  'headers' => $this->AuthService->makeHeadersUser($user)  ,
        'json' => $location]);
    } catch(ClientException $exeption) {$user = $exeption->getResponse();}

     return  json_decode($user->getBody()->read(10284));  
  
    }

    
}