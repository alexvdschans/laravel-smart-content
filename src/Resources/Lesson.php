<?php namespace AvdS\SmartContent\Resources;

class Lesson extends SearchMap {

    public $model; 
    
    public $parent;

    public $type = 'lesson';

    public function __construct($model)
    {
        $this->model = $model;
    }
        
    public function getRecord()
    {
        // A comment will return the entire mapping of the parent
        $record = [
            "type" => "course",
            "id" => $this->model->id,
            "slug" => $this->model->slug,
            "title" => $this->model->title,
            "content" => strip_tags($this->model->content),
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