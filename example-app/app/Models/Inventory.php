<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Inventory extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventoryNo',
        'description',
        'count',
        'condition',
        'serialNo',
        'lendability',
        'image',
        'price',
    ];
    public $timestamps = false;

public function getImageAttribute($value)
    {
        // Check if the image exists and return the public URL, else return the original value
        if ($value) {
            return Storage::url($value);
        }

        return null; // Return null or a default image URL if the image doesn't exist
    }
}
