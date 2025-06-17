<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetworkType extends Model
{
    use SoftDeletes;
    //

    protected $fillable = [
        'name',
    ];

    public static function getNameById($id)
    {
        return static::find($id)->name ?? null;
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'network_type_id');
    }
}
