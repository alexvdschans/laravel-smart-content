<?php namespace AvdS\SmartContent\Traits;

use AvdS\SmartContent\Facades\SmartContent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

trait Indexable {

    public function updateIndex()
    {
        // TODO: check if domain is set in the model, to set the index. Mainly to support Phoenix
        // TODO: mapClass different ways of writing: camelcase, uppercase, 
        $model = $this;
        $mapClass   = "AvdS\\SmartContent\\Resources\\" . class_basename($model);
        $map        = new $mapClass($model);

        $schema     = $map->getSchema();
        $record     = $map->getRecord();

        $use_internal_links = isset($model->use_internal_links) && isset($model->internal_link_field) ? ($model->use_internal_links) : (false);

        if(method_exists($model,'internal_links_extra_keywords')) {
            $extra_keywords = $model->internal_links_extra_keywords();
        } else {
            //hardcoded example for now
            $extra_keywords = [[
                'keyword' => 'king',
                'url' => 'https://imu.nl/'
            ]];
        }

        if(method_exists($model,'internal_links_remove_keywords')) {
            $remove_keywords = $model->internal_links_remove_keywords();
        } else {
            $remove_keywords = [];
        }

        $index_data = [
            'data'      => [$record],
            'schema'    => [
                'index' => SmartContent::getIndex(),
                'type' => $map->type,
                'mappings' => $schema
            ],
            //hardcoded webhook url for now
            'webhook_url' => 'http://editor-douwe.phoenix-dev1.imu.nl/search/web_hook_callback'
        ];

        if($use_internal_links) {
            $index_data['use_internal_links'] = true;
            $index_data['internal_link_settings'] = [
                'ids' => [$model->id],
                'extra_keywords' => $extra_keywords,
                'remove_keywords' => $remove_keywords
            ];
        } else {
            $index_data['use_internal_links'] = false;
        }

        SmartContent::index($index_data);
    }
    
    public function deleteIndex()
    {
        $model = $this;
        $mapClass   = "AvdS\\SmartContent\\Resources\\" . class_basename($model);

        SmartContent::delete([
            'index' => SmartContent::getIndex(),
            'doc_id' => $model->id,
            'type' => $mapClass
        ]);
        
    }
    
    public function scopeRelated($query)
    {
        $model = $this;
        
        $related = SmartContent::related([
            'index' => SmartContent::getIndex(),
            'type' => $model->mapClass,
            'doc_id' => $model->id,
            'page' => 1,
            'per_page' => 5
        ]);
       
        return $this->hydrate($query, $related);
        
    }
    
    public function scopeSearch($query, $search)
    {
        
        $model = $this;
        
        $result = SmartContent::search([
            'index' => SmartContent::getIndex(),
            'type' => $model->mapClass,
            'query' => $search,
        ]);
      
        return $this->hydrate($query, $result);
        
    }
    
    public function scopeFindInIndex($query, $id)
    {
        
        $model = $this;
        
        $result = SmartContent::search([
            'index' => SmartContent::getIndex(),
            'type' => $model->mapClass,
            'filters' => 'id:' . $id,
            'query' => '',
        ]);
        
        return $this->hydrate($query, $result);
        
    }
    
    protected function hydrate($query, $result)
    {
        
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
            if(!isset($model->deleted_at) && (!isset($model->status) || (isset($model->status) && $model->status != 'archived'))) {
                if (getenv('APP_ENV') == 'local' || getenv('APP_ENV') == 'dev') {
                    Log::info('Debug - smart content - updating Index');
                }
                $model->updateIndex();
            } elseif(isset($model->status) && $model->status == 'archived') {
                if (getenv('APP_ENV') == 'local' || getenv('APP_ENV') == 'dev') {
                    Log::info('Debug - smart content - deleting Index');
                }
                $model->deleteIndex();
            }
        });
            
        static::deleted(function($model) {
            if(isset($model->mapParent)){
                if (getenv('APP_ENV') == 'local' || getenv('APP_ENV') == 'dev') {
                    Log::info('Debug - smart content - updating Index');
                }
                $model->updateIndex();
            } else {
                if (getenv('APP_ENV') == 'local' || getenv('APP_ENV') == 'dev') {
                    Log::info('Debug - smart content - deleting Index');
                }
                $model->deleteIndex();
            }
            
        });
                
    }
}
