<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rental extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'rental';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventoryId',
        'name',
        'adress',
        'email',
        'deposit',
        'phone',
        'dueDate',
        'returnDate',
        'comment',
        'receivingUser',
        'status',
    ];
    public $timestamps = false;

    protected $with = ['inventory'];

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'id', 'inventoryId');
    }
}



//$table->date('borrowDate');

//$table->unsignedBigInteger('lendingUser');

