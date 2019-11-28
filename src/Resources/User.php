<?php namespace AvdS\SmartContent\Resources;

class User extends SearchMap {

    public $model; 
    
    public $parent;

    public $type = 'user';

    public function __construct($model)
    {
        $this->model = $model;
    }
        
    public function getRecord()
    {
        
        $user = $this->model;
        
        // A comment will return the entire mapping of the parent
        $record = [
            'type'          => "user",
            'id'   			=> (int) $user->id,
            'firstname' 	=> (string) $user->firstname,
            'lastname' 	    => (string) $user->lastname,
            'fullname'      => (string) $user->fullname,
            'avatar' 		=> (string) $user->picture(80),
            'username' 		=> (string) $user->username,
            'email' 		=> (string) $user->email,
            'created_at' 	=> $user->created_at->toDateTimeString(),
            "tenant"        => tenant()->internal_domain,
        ];

        return $record;
        
    }
    
    public function getSchema()
    {
        $schema = [
            "type" => "keyword",
            "id" => "long",
            "firstname" => "text",
            "lastname" => "text",
            "fullname" => "text",
            "avatar" => "keyword",
            "username"	=> "text",
            "email"	=> "text",
            "created_at" => "date",
            "tenant" => "keyword"
        ];

        return $schema;
        
    }
    
}