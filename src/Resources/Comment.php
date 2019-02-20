<?php namespace AvdS\SmartContent\Resources;

class Comment extends SearchMap {

    public $model; 
    
    public $parent;
    
    public function __construct($model)
    {
        
        $this->model = $model;
        
        $this->parent = $model->conversation;
        
    }
        
    public function getRecord()
    {
        // A comment will return the entire mapping of the parent
        $record = [
            "type" => "topic",
            "id" => $this->parent->id,
            "slug" => $this->parent->slug,
            "title" => $this->parent->title,
            "board" => $this->parent->channel->name,
            "content" => $this->parent->post->first()->content,
            "author" => $this->parent->user->fullname,
            "created_at" => $this->parent->created_at->toDateTimeString(),
            "posts" => []
        ];
        
        foreach($this->parent->post as $index => $post){
            if($index > 0){
                $record['posts'][] = [
                    "author" => $post->user->fullname,
                    "content" => $post->content
                ];
            }
        }
        
        return $record;
        
    }
    
    public function getSchema()
    {
       
        return [
            "id" => "long",
            "type" => "keyword",
            "slug" => "keyword",
            "title" => "long",
            "category" => "keyword",
            "content" => "text",
            "author" => "keyword",
            "created_at" => "date",
        ];
        
    }
    
}