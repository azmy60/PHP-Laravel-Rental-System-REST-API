<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

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
        'lendability'
    ];
    public $timestamps = false;
}
