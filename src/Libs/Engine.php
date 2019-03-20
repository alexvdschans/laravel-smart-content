<?php namespace AvdS\SmartContent\Libs;

use GuzzleHttp;
use AvdS\SmartContent\Exceptions\TokenNotSetException;
use AvdS\SmartContent\Exceptions\GeneralShardFailureException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class Engine {
    
    protected $token;
    
    protected $index;
    
    protected $client;
    
    protected $headers;

    protected $api_url;

    public function __construct($config) 
    {
        $this->api_url = config('smartcontent.api_url');

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

            $index = $params['index'];
            $query = $params['query'];
            $fields = $params['fields'];
            $type = $params['type'];
            
            $orderBy = 'created_at:desc,commentsCount:desc';

            $data = [
                'index'     => $index,
                'page'      => 1,
                'per_page'  => 5,
//                 'order_by'  => $orderBy,
                'fields'    => $fields,
                'type'      => $type,
                'query'     => $query
            ];
            
            $string = http_build_query($data);

            $res = $this->client->request('GET', '/api/v1/search?' . $string,
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

        if(isset($data['use_internal_links']) && $data['use_internal_links'] == true) {
            $url .= '?internal_links=true&internal_link_field=content';
        }

        $request = $this->client->request('POST', $url, [
            'form_params'   => $data,
            'headers'       => $this->headers
        ]);

        Log:info($request->getBody());

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
