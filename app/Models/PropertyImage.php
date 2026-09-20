<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'path',
        'thumb_path',
        'alt',
        'sort_order',
        'is_cover',
        'rotation',
    ];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'rotation' => 'integer',
        ];
    }

    /**
     * Property for the image.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
