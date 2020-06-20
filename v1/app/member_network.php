<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class member_network extends Model
{
    protected $table = 'member-network';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $dateFormat = 'U';
}
