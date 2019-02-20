<?php

namespace AvdS\SmartContent\Facades;

use Illuminate\Support\Facades\Facade;

class SmartContent extends Facade {

    public static function getFacadeAccessor() {
    
        return 'sc';
    
    }

}