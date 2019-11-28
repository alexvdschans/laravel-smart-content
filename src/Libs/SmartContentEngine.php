<?php namespace AvdS\SmartContent\Libs;

use GuzzleHttp;
use AvdS\SmartContent\Exceptions\TokenNotSetException;
use AvdS\SmartContent\Exceptions\GeneralShardFailureException;
use GuzzleHttp\Exception\ServerException;

CONST API_URL = 'http://search.thehuddle.nl';

class SmartContentEngine {
    
    protected $token;
    
    protected $index;
    
    protected $client;
    
    protected $headers;

    protected $api_url;

    public function __construct($config) 
    {
        $this->api_url = API_URL;

        $this->token = isset($config['token']) ? $config['token'] : null;
       
        $this->index = isset($config['index']) ? $config['index'] : null;

        $this->client = new GuzzleHttp\Client(['base_uri' => $this->api_url]);
        
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
        ];
        
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

            $defaults = [
                    'page' => 1,
                    'per_page' => 5,
                    'fields' => 'content,title,slug',
            ];

            $params = array_merge($defaults, $params);

            $data = [
                'query'     => $params['query'],
                'index'     => $params['index'],
                'type'      => $params['type'],
                'fields'    => $params['fields'],
                'page'      => $params['page'],
                'per_page'  => $params['per_page'],
            ];

            if(isset($params['order_by'])){                
                $data['order_by']  = $params['order_by'];
            }
            if(isset($params['filters']) && count($params['filters']) > 0){
                $data['filters']  = $params['filters'];
            }
            if(isset($params['min'])){
                $data['min_timestamp']  = $params['min'];
            }
            if(isset($params['max'])){
                $data['max_timestamp']  = $params['max'];
            }
            
            $data['filters'] = 'tenant:' . tenant()->internal_domain;
            
            $string = http_build_query($data);

            $url = '/api/v1/search?' . $string;
           
            $res = $this->client->request('GET', $url,
                    ['headers' => $this->headers]
            );
            
            $data = json_decode($res->getBody())->data;
            
            return $data;
            
        } catch (ServerException $e) {
            throw new GeneralShardFailureException();
        }
        
    }
    
    public function index($data)
    {

        $url = '/api/v1/index';

        if($data['use_internal_links'] == true) {
            $url .= '?internal_links=true&internal_link_field=content';
        }

        $request = $this->client->request('POST', $url, [
            'form_params'   => $data,
            'headers'       => $this->headers
        ]);
        
        return true;
    }   
    
    public function delete($params)
    {
       
        $data = [
            'index' => $params['index'],
            'type' => $params['type'],
            'doc_id' => $params['doc_id']
        ];
        
        $string = http_build_query($data);
        
        $request = $this->client->request('DELETE', '/api/v1/documents?' . $string, 
            ['headers' => $this->headers]
        );
        
        return true;
    }   
    
    public function related($params)
    {

        $data = [
            'index' => $params['index'],
            'type' => $params['type'],
            'doc_ids' => $params['doc_id'],
            'page' => $params['page'],
            'per_page' => $params['per_page'],
        ];

        $string = http_build_query($data);
        
        $request = $this->client->request('GET', '/api/v1/documents/related?' . $string,
            ['headers' => $this->headers]
        );
        
        $data = json_decode($request->getBody())->data;
        
        return $data;
    }   
    
    public function search($params)
    {
        return $this->call($params);
    }
    
}
