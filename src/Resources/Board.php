<?php namespace AvdS\SmartContent\Resources;

class Board extends SearchMap {

    public $model; 
    
    public $parent;

    public $type = 'board';

    public function __construct($model)
    {
        $this->model = $model;
    }
        
    public function getRecord()
    {
        // A comment will return the entire mapping of the parent
        $record = [
            "type" => "board",
            "id" => $this->model->id,
            "slug" => $this->model->slug,
            "title" => $this->model->name,
            "content" => $this->model->description,
            "tenant" => tenant()->internal_domain,

        ];

        return $record;
    }
    
    public function getSchema()
    {
        $schema = [
            "id" => "long",
            "type" => "keyword",
            "slug" => "keyword",
            "title" => "text",
            "content" => "text",
            "tenant" => "keyword",

        ];

        return $schema;
        
    }
    
}