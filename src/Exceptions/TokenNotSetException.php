<?php

namespace AvdS\SmartContent\Exceptions;

use \Exception;

const MESSAGE  = 'No token set';

class TokenNotSetException extends Exception implements SmartContentExceptionInterface
{
    //
    
    public function __construct()
    {
        
        parent::__construct(MESSAGE);
        
    }
    
}
