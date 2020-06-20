<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_hashTag extends Model
{
    protected $table = 'memberhastag';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
