<?php namespace AvdS\SmartContent\Traits;

use AvdS\SmartContent\Facades\SmartContent;
use Illuminate\Pagination\LengthAwarePaginator;

trait Indexable {

    public function updateIndex()
    {
        
        // TODO: check if domain is set in the model, to set the index. Mainly to support Phoenix
        
        // TODO: mapClass different ways of writing: camelcase, uppercase, 
        $model = $this;
        $mapClass   = "AvdS\\SmartContent\\Resources\\" . ucfirst($model->mapClass);
        $map        = new $mapClass($model);
        
        $schema     = $map->getSchema();
        $record     = $map->getRecord();

        SmartContent::index([
            'data'      => [ $record ],
            'schema'    => [
                'index' => SmartContent::getIndex(),
                'mappings' => $schema
            ]
        ]);
        
    }
    
    public function deleteIndex()
    {
        $model = $this;
        
        SmartContent::delete([
            'index' => SmartContent::getIndex(),
            'doc_id' => $model->id,
            'type' => $model->mapClass
        ]);
        
    }
    
    public function findRelated()
    {
        $model = $this;
        
        $related = SmartContent::related([
            'doc_id' => $model->id,
            'index' => SmartContent::getIndex(),
            'type' => $model->mapClass
        ]);
        
        return $related;
    }
    
    public function scopeSearch($query, $search)
    {
        
        $model = $this;
        
        $result = SmartContent::search([
            'query' => $search,
            'index' => SmartContent::getIndex(),
            'type' => $model->mapClass
        ]);
      
        if(in_array(config('smartcontent.hydrate'), ['raw', ''])){
            return $result;
        }
       
        $ids = [];
        
        foreach($result->search_data as $item){
          $ids[] = $item->id;
        }
        
        $queryObject = $query->whereIn('id', $ids)->get();
        
        if(config('smartcontent.hydrate') == 'collection'){
            return $queryObject;
        }
        
        if(config('smartcontent.hydrate') == 'paginate'){
            
            $total = $result->meta->pagination->total_results;
            
            $paginator = new LengthAwarePaginator($queryObject, $total, 5);
            
            return $paginator;
            
        }
        
        
    }
    
    protected static function bootIndexable() {
        
        static::saved(function($model) {
            
            if(!isset($model->deleted_at)){
                $model->updateIndex();
            } 
            
        });
            
        static::deleted(function($model) {
            
            if(isset($model->mapParent)){
                $model->updateIndex();
            } else {
                $model->deleteIndex();
            }
            
        });
                
    }
}
