<?php

namespace Parsidev\MeliPayamak\Facades;

use Illuminate\Support\Facades\Facade;

class MeliPayamak extends Facade {

    protected static function getFacadeAccessor() {
        return 'melipayamak';
    }
}
