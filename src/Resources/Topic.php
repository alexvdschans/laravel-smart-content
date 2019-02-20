<?php namespace AvdS\SmartContent\Resources;

class Topic extends SearchMap {
    
    public function __construct($model)
    {
       parent::__construct($model);
    }
        
    public function getRecord()
    {
        
        $record =  [
            "type" => "topic",
            "id" => $this->model->id,
            "slug" => $this->model->slug,
            "title" => $this->model->slug,
            "board" => $this->model->channel->name,
            "content" => $this->model->post->first()->content,
            "author" => $this->model->user->fullname,
            "created_at" => $this->model->created_at->toDateTimeString(),
            "posts" => []
        ];
        
        foreach($this->model->post as $index => $post){
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