<?php namespace AvdS\SmartContent\Resources;

class Blogpost extends SearchMap {

    public $model; 
    
    public $parent;

    public $type = 'blogpost';

    private $use_internal_links;

    public function __construct($model)
    {
        $this->use_internal_links = isset($model->use_internal_links) && isset($model->internal_link_field) ? ($model->use_internal_links) : (false);
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
            'website_id' => $this->model->website_id,
        ];

        if($this->use_internal_links) {
            $record['internal_links'] = true;
            $record['internal_link_field'] = $this->model->internal_link_field;
        }

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
            "author" => "keyword",
        ];

        if($this->use_internal_links) {
            $schema['internal_links'] = true;
            $schema['internal_link_field'] = $this->model->internal_link_field;
        }

        return $schema;
        
    }
    
}