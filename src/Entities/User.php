<?php

namespace Src\Entities;

class User{

    private $id;

    private $email;

    private $username;

    private $role;

    private $token;

    private $refresh_token;

    private $roles;

    private $updatedAt;

    private $confirmed;

    private $name;

    private $firstName;

    private $coverPath;

    private $rank;

    private $phone;

    private $clientGames;

    private $clientInfiniteQr;

    private $bagNumber;

    private $exploreCoin;

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;

        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;

        return $this;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;

        return $this;
    }

    public function getToken(){
        return $this->token;
    }

    public function setToken($token){
        $this->token = $token;

        return $this;
    }

    public function getRefresh_token(){
        return $this->refresh_token;
    }

    public function setRefresh_token($refresh_token){
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getRole(){
        return $this->role;
    }

    public function setRole($role){
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of roles
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of confirmed
     */ 
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set the value of confirmed
     *
     * @return  self
     */ 
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of firstName
     */ 
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */ 
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of coverPath
     */ 
    public function getCoverPath()
    {
        return $this->coverPath;
    }

    /**
     * Set the value of coverPath
     *
     * @return  self
     */ 
    public function setCoverPath($coverPath)
    {
        $this->coverPath = $coverPath;

        return $this;
    }

    /**
     * Get the value of rank
     */ 
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set the value of rank
     *
     * @return  self
     */ 
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of clientGames
     */ 
    public function getClientGames()
    {
        return $this->clientGames;
    }

    /**
     * Set the value of clientGames
     *
     * @return  self
     */ 
    public function setClientGames($clientGames)
    {
        $this->clientGames = $clientGames;

        return $this;
    }

    /**
     * Get the value of clientInfiniteQr
     */ 
    public function getClientInfiniteQr()
    {
        return $this->clientInfiniteQr;
    }

    /**
     * Set the value of clientInfiniteQr
     *
     * @return  self
     */ 
    public function setClientInfiniteQr($clientInfiniteQr)
    {
        $this->clientInfiniteQr = $clientInfiniteQr;

        return $this;
    }

    /**
     * Get the value of bagNumber
     */ 
    public function getBagNumber()
    {
        return $this->bagNumber;
    }

    /**
     * Set the value of bagNumber
     *
     * @return  self
     */ 
    public function setBagNumber($bagNumber)
    {
        $this->bagNumber = $bagNumber;

        return $this;
    }

    /**
     * Get the value of exploreCoin
     */ 
    public function getExploreCoin()
    {
        return $this->exploreCoin;
    }

    /**
     * Set the value of exploreCoin
     *
     * @return  self
     */ 
    public function setExploreCoin($exploreCoin)
    {
        $this->exploreCoin = $exploreCoin;

        return $this;
    }
}
