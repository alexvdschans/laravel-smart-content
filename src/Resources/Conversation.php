<?php namespace AvdS\SmartContent\Resources;

class Conversation extends SearchMap {

    public $type = 'conversation';

    public function __construct($model)
    {
       parent::__construct($model);
    }
        
    public function getRecord()
    {
        
        if(isset($this->model->user) && isset($this->model->channel) && $this->model->post()->first()){
            $record =  [
                "type" => $this->type,
                "id" => $this->model->id,
                "slug" => $this->model->slug,
                "title" => $this->model->title,
                "board" => $this->model->channel->name,
                "content" => $this->model->post->first()->content,
                "author" => $this->model->user->fullname,
                "created_at" => $this->model->created_at->toDateTimeString(),
                "posts_content" => "",
                "tenant" => tenant()->internal_domain,
                "posts" => [],
            ];
            
            foreach($this->model->post as $index => $post){
                if($index > 0 && isset($post->user) AND $index < 100){
                    $record['posts'][] = [
                        "author" => $post->user->fullname,
                        "content" => $post->content
                    ];
                    $record['posts_content'] .=  ' ' . $post->content;
                }
            }
            
            return $record;
        }
        
        return false;
    }
    
    public function getSchema()
    {
       
        return [
            "id" => "long",
            "type" => "keyword",
            "slug" => "keyword",
            "title" => "text",
            "category" => "keyword",
            "content" => "text",
            "author" => "keyword",
            "created_at" => "date",
            "posts" => "nested",
            "posts_content" => "text",
            "tenant" => "keyword"
        ];
        
    }
    
}