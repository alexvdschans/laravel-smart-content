<?php

namespace AvdS\SmartContent\Exceptions;

use \Exception;

const MESSAGE  = 'The backend received your request, but was unable to parse it.';

class GeneralShardFailureException extends \Exception implements SmartContentExceptionInterface
{
    //
    
    public function __construct()
    {
        
        parent::__construct(MESSAGE);
        
    }
    
}
