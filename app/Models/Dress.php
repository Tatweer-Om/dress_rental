<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dress extends Model
{
    use HasFactory;

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_name');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_name');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_name');

    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_name');

    }
}
