<?php
namespace Src\Services;
require  '././vendor/autoload.php';

class MappingServices {

    public function map($data, $class){
       
        $object = new $class();
        foreach($data as $key => $value){
            $setName = 'set' . ucfirst($key);
            $object->$setName($value);
        }
        return $object;
    }
}