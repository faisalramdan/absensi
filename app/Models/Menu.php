<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'url',
        'permission',
        'sort',
        'parent_id',
        'status'
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('status', true)
            ->orderBy('sort');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}
