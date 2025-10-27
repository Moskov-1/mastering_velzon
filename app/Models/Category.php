<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    public static function _types(){
        return [
            
            'BREAKFAST' => 'breakfast',
            'DINNER'=> 'dinner',
        
        ];
    }

    public static function _status(){
        return [
            'ACTIVE' => '1',
            'INACTIVE'=> '0',
        ];
    }

    public function parent(){
        return $this->belongsTo(Category::class,'parent_id');
    }
}
