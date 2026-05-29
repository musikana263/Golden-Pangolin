<?php

namespace App;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
