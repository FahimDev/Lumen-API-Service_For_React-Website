<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class webTitle extends Model
{
    protected $table = 'web_title';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
