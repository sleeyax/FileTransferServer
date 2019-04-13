<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getByKey(string $key) {
        return self::where('key', $key)->first();
    }
}
