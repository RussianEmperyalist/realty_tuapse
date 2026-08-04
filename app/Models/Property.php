<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'legacy_id',
        'employee_id',
        'title',
        'slug',
        'deal_type',
        'property_type',
        'city',
        'address',
        'price',
        'price_label',
        'currency',
        'rooms',
        'floor',
        'floors_total',
        'square',
        'windows',
        'description',
        'latitude',
        'longitude',
        'phone_override',
        'is_published',
        'is_featured',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'square' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Employee who owns the listing.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Images attached to the listing.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /**
     * Incoming messages from the object page.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(PropertyMessage::class);
    }

    /**
     * Cover image if one exists.
     */
    public function coverImage(): HasMany
    {
        return $this->images()->where('is_cover', true);
    }
}
