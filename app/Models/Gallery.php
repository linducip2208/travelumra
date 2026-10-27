<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Gallery extends Model
{
    protected $fillable = ['galleriable_type', 'galleriable_id', 'file_path', 'caption', 'sort_order'];

    public function galleriable(): MorphTo
    {
        return $this->morphTo();
    }
}
