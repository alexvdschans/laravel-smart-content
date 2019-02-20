<?php namespace AvdS\SmartContent\Libs;

use GuzzleHttp;
use AvdS\SmartContent\Exceptions\TokenNotSetException;
use AvdS\SmartContent\Exceptions\GeneralShardFailureException;
use GuzzleHttp\Exception\ServerException;

CONST API_URL = 'http://search.thehuddle.nl';

class Engine {
    
    protected $token;
    
    protected $index;
    
    public function __construct($config) 
    {
        
        $this->token = isset($config['token']) ? $config['token'] : null;
       
        $this->index = isset($config['index']) ? $config['index'] : null;
        
    }
    
    public function setIndex($index)
    {
        $this->index = $index;
    }
    
    public function getIndex()
    {
        return $this->index;
    }
    
    public function call($params)
    {
     
        if(!isset($this->token) OR $this->token == ""){
            throw new TokenNotSetException();
        }
        
        try {
            $index = $params['index'];
            $query = $params['query'];
            
            $client = new GuzzleHttp\Client(['base_uri' => API_URL]);
            
            $headers = [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept'        => 'application/json',
            ];
            
            $data = [
                'index'     => $index,
                'page'      => 1,
                'per_page'  => 5,
//                 'order_by'  => 'created_at:desc,commentsCount:desc',

                'fields'    => 'title,content',
                'type'      => 'blogpost',
                'query'     => $query
            ];
            
            $string = http_build_query($data);
            
            $res = $client->request('GET', '/api/v1/search?' . $string,
                    ['headers' => $headers]
            );
            
            $data = json_decode($res->getBody())->data;
            
            return $data;
        
        } catch (ServerException $e) {
            throw new GeneralShardFailureException();
        }
        
    }
    
    public function index($data)
    {
       
        $client = new GuzzleHttp\Client(['base_uri' => API_URL]);
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
        ];
        
        $request = $client->request('POST', '/api/v1/index', [
            'form_params'   => $data,
            'headers'       => $headers
        ]);
        
        $result = json_decode($request->getBody());
        
    }   
    
    public function delete($params)
    {
      
        $client = new GuzzleHttp\Client(['base_uri' => API_URL]);
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
        ];
        
        $data = [
            'index' => $params['index'],
            'type' => $params['type'],
            'doc_id' => $params['doc_id']
        ];
        
        $string = http_build_query($data);
        
        $request = $client->request('DELETE', '/api/v1/documents?' . $string, 
            ['headers' => $headers]
        );
        
        $result = json_decode($request->getBody());
        
    }   
    
    public function search($params)
    {
        
        return $this->call($params);
        
    }
    
    
}
