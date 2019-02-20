<?php namespace AvdS\SmartContent\Resources;

class Blogpost extends SearchMap {

    public $model; 
    
    public $parent;
    
    public function __construct($model)
    {
        
        $this->model = $model;
        
    }
        
    public function getRecord()
    {
        // A comment will return the entire mapping of the parent
        $record = [
            "type" => "blogpost",
            "id" => $this->model->id,
            "slug" => $this->model->slug,
            "title" => $this->model->title,
            "content" => $this->model->content,
            "author" => $this->model->author,
            
        ];
        
        return $record;
        
    }
    
    public function getSchema()
    {
       
        return [
            "id" => "long",
            "type" => "keyword",
            "slug" => "keyword",
            "title" => "long",
            "content" => "text",
            "author" => "keyword",
            
        ];
        
    }
    
}