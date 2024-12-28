# Source code laravel - Sewa Tenda

Kelompok 6 Sewa Tenda Kelas D

1. Mohammad Azmi-193
2. Muhamad Bustomi-183
3. Adi Sutisna-185
4. Mohammad Ridwan-182
5. Gilang Sugiarto Putra-250

Tersedia juga di github => [https://github.com/azmy60/PHP-Laravel-Rental-System-REST-API/](https://github.com/azmy60/PHP-Laravel-Rental-System-REST-API/)

## Routes

### `routes/api.php`
```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', "\App\Http\Controllers\AuthController@login");
Route::post('register', "\App\Http\Controllers\AuthController@register");

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user/self', "\App\Http\Controllers\UserController@getSelf");
    Route::get('user', "\App\Http\Controllers\UserController@getAll");
    Route::get('user/{id}', "\App\Http\Controllers\UserController@get");
    Route::post('user', "\App\Http\Controllers\UserController@create");
    Route::put('user', "\App\Http\Controllers\UserController@update");
    Route::delete('user/{id}', "\App\Http\Controllers\UserController@delete");

    Route::get('inventory', "\App\Http\Controllers\InventoryController@getAll");
    Route::get('inventory/{id}', "\App\Http\Controllers\InventoryController@get");
    Route::post('inventory', "\App\Http\Controllers\InventoryController@create");
    Route::put('inventory/{id}', "\App\Http\Controllers\InventoryController@update");
    Route::delete('inventory/{id}', "\App\Http\Controllers\InventoryController@delete");

    Route::get('inventory/{id}/rental','\App\Http\Controllers\RentalController@getAllRentalByInventoryId');
    Route::get('rental', "\App\Http\Controllers\RentalController@getAll");
    Route::get('rental/{id}', "\App\Http\Controllers\RentalController@get");
    Route::post('rental', "\App\Http\Controllers\RentalController@create");
    Route::put('rental/{id}', "\App\Http\Controllers\RentalController@update");
    Route::delete('rental/{id}', "\App\Http\Controllers\RentalController@delete");
});
```

<div style="page-break-after: always;"></div>

## Models

### `app/Models/Inventory.php`

```php
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
```

<div style="page-break-after: always;"></div>

### `app/Models/Rental.php`

```php
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

```

<div style="page-break-after: always;"></div>

### `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phoneNumbers',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    public $timestamps = false;
}
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     *
     *   protected $casts = [
    'email_verified_at' => 'datetime',
    ];
    }
     */
```

## Controllers

<div style="page-break-after: always;"></div>

### `app/Http/Controllers/AuthController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:user,username',
            'email' => 'required|string|unique:user,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,

            'token' => $token
        ];

        return response($response, 201);

    }

    public function login(Request $request) {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',

        ]);

        // Check email
        $user = User::where('username', $fields['username'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;


        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();


        return [
            'message' => 'Logged out'

        ];
    }
}
```

<div style="page-break-after: always;"></div>

### `app/Http/Controllers/RentalController.php`

```php
<?php


namespace App\Http\Controllers;


use App\Models\Inventory;
use App\Models\Rental;
use http\Exception\BadRequestHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class RentalController extends Controller
{
    public function getAllRentalByInventoryId($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        $inventory = Inventory::findOrFail($id);
        return response(Rental::where('inventoryId', $inventory->id)->where('returnDate', null)->get()->toJson(JSON_PRETTY_PRINT));
    }

    public function getAll()
    {
        if (auth()->user()->username == 'admin') {
            return response(Rental::get()->toJson(JSON_PRETTY_PRINT));
        }

        return response(Rental::where('receivingUser', auth()->user()->id)->get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        return response(Rental::findOrFail($id)->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        $inventory = Inventory::findOrFail($request->inventoryId);
        if($inventory->lendability != 1) {
            throw new PreconditionFailedHttpException("The inventory with the given id of " .$request->inventoryId. " is not lendable!");
        }
        if($inventory->count <= 0) {
            throw new PreconditionFailedHttpException("There is no more inventory to lend with the given id of " .$request->inventoryId. "!");
        }
        // \Log::info($request->all());
        // exit;
        $validator = Validator::make($request->all(),[
            'inventoryId' => 'required|exists:inventory,id',
            'name' => 'required|max:45',
            'adress' => 'required|max:45',
            'email' => 'required|email|max:45',
            'deposit' => 'required|numeric|min:0',
            'phone' => 'required|max:45',
            'borrowDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'dueDate' => [
                'required',
                'date_format:Y-m-d',
                'after:borrowDate',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->has('borrowDate')) {
                        $borrowDate = \Carbon\Carbon::parse($request->borrowDate);
                        $dueDate = \Carbon\Carbon::parse($value);

                        if ($dueDate->diffInDays($borrowDate) > 31) {
                            $fail('The due date must not be more than one month after the borrow date.');
                        }
                    }
                },
            ],
            'comment' => 'max:45',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }
        $inventory->count -= 1;
        if(!$inventory->save()) {
            throw new BadRequestHttpException("Couldn't update the inventory count!");
        }
        $rental = new Rental;
        $rental->inventoryId = $request->inventoryId;
        $rental->name = $request->name;
        $rental->adress = $request->adress;
        $rental->email = $request->email;
        $rental->deposit = $request->deposit;
        $rental->phone = $request->phone;
        $rental->borrowDate = $request->borrowDate;
        $rental->dueDate = $request->dueDate;
        $rental->comment = $request->comment;
        $rental->lendingUser = auth()->user()->id;
        $rental->receivingUser = auth()->user()->id;
        $rental->status = 'processing';
        if($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new rental!");
        }
    }

    public function update($id, Request $request)
    {
        $rental = Rental::findOrFail($id);

        if (auth()->user()->username != 'admin' && auth()->user()->id != $rental->receivingUser) {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        // Update validation rules
        $validator = Validator::make($request->all(), [
            'inventoryId' => 'nullable|exists:inventory,id',

            'name' => 'nullable|max:45',
            'adress' => 'nullable|max:45',
            'email' => 'nullable|email|max:45',

            'deposit' => 'nullable|numeric|min:0',
            'phone' => 'nullable|max:45',

            'borrowDate' => 'nullable|date_format:Y-m-d|after_or_equal:now',
            'dueDate' => 'nullable|date_format:Y-m-d|after:borrowDate',
            'returnDate' => 'nullable|date_format:Y-m-d',
            'comment' => 'nullable|max:45',

            'status' => 'nullable|in:processing,shipping,delivered,returning,returned',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        // Update only the provided fields
        foreach ($request->all() as $key => $value) {
            if ($value !== null && $rental->isFillable($key)) {
                $rental->$key = $value;
            }
        }


        // Attempt to save changes
        if ($rental->save()) {
            return response($rental->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't save the updated rental!");
        }
    }

    public function delete($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        $rental = Rental::findOrFail($id);
        $inventory = Inventory::findOrFail($rental->inventoryId);
        if(!$rental->delete()) {
            throw new BadRequestHttpException("Couldn't delete the rental count!");
        }
        $inventory->count += 1;
        if(!$inventory->save()) {
            throw new BadRequestHttpException("Couldn't update the inventory count!");
        }
    }
}
```

<div style="page-break-after: always;"></div>

### `app/Http/Controllers/UserController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends Controller
{
    public function getAll()
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }
        return response(User::get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        if (auth()->user()->id != $id) {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }
        return response(User::where('id', $id)->first()->toJson(JSON_PRETTY_PRINT));
    }

    public function getSelf()
    {
        $id = auth()->user()->id;
        // echo json_encode(User::where('id', $id)->first());
        // exit;
        return response(User::where('id', $id)->first()->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:45',
            'email' => 'required|email|unique:user|max:45',
            'password' => 'required|max:256'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }
        $user = new User;
        $user->name = $request->name;
        $user->username = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        if($user->save()) {
            return response($user->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new user!");
        }
    }

    public function update(Request $request)
    {
        $id = auth()->user()->id;
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:45',
            'username' => 'nullable|max:45|unique:user,username,' . $id,
            'phone_numbers' => 'nullable|max:45',
            'email' => 'nullable|email|unique:user,email,' . $id . '|max:45',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('phone_numbers')) {
            $user->phone_numbers = $request->phone_numbers;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($user->save()) {
            return response($user->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't update the user!");
        }
    }

    public function delete($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        if(User::findOrFail($id)->delete()) {
            throw new BadRequestHttpException("Couldn't delete the user!");
        }
    }
}
```

<div style="page-break-after: always;"></div>

### `app/Http/Controllers/InventoryController.php`

```php
<?php


namespace App\Http\Controllers;


use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InventoryController extends Controller
{
    public function getAll()
    {
        return response(Inventory::get()->toJson(JSON_PRETTY_PRINT));
    }

    public function get($id)
    {
        return response(Inventory::findOrFail($id)->toJson(JSON_PRETTY_PRINT));
    }

    public function create(Request $request)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'inventoryNo' => 'required|unique:inventory|max:45',
            'description' => 'required|max:45',
            'count' => 'required|numeric|min:0',
            'condition' => 'required|numeric|min:1|max:5',
            'serialNo' => 'required|max:45',
            'lendability' => 'required|numeric|min:0|max:1',
            'price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }
        $inventory = new Inventory;
        $inventory->inventoryNo = $request->inventoryNo;
        $inventory->description = $request->description;
        $inventory->count = $request->count;
        $inventory->condition = $request->condition;
        $inventory->serialNo = $request->serialNo;
        $inventory->lendability = $request->lendability;
        if ($inventory->save()) {
            return response($inventory->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't create the new inventory!");
        }
    }

    public function update($id, Request $request)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        $inventory = Inventory::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'inventoryNo' => 'required|max:45|unique:inventory,inventoryNo,' . $id,
            'description' => 'required|max:45',
            'count' => 'required|numeric|min:0',
            'condition' => 'required|numeric|min:1|max:5',
            'serialNo' => 'required|max:45|unique:inventory,serialNo,' . $id,
            'lendability' => 'required|numeric|min:0|max:1',
            'price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }
        $inventory->inventoryNo = $request->inventoryNo;
        $inventory->description = $request->description;
        $inventory->count = $request->count;
        $inventory->condition = $request->condition;
        $inventory->serialNo = $request->serialNo;
        $inventory->lendability = $request->lendability;
        if($inventory->save()){
            return response($inventory->toJson(JSON_PRETTY_PRINT));
        } else {
            throw new BadRequestHttpException("Couldn't update the inventory!");
        }
    }

    public function delete($id)
    {
        if (auth()->user()->username != 'admin') {
            // not allowed
            return response()->json(['message' => 'You are not authorized to access this resource.'], 401);
        }

        if (Inventory::findOrFail($id)->delete()) {
                throw new BadRequestHttpException("Couldn't delete the inventory!");
        }
    }
}
```

<div style="page-break-after: always;"></div>

### `app/Http/Controllers/Controller.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
```

## Migration

<div style="page-break-after: always;"></div>

### database/migrations/2014_09_12_000000_create_user_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('email', 45)->unique();
            $table->string('password', 45);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2014_10_12_100000_create_inventory_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('inventoryNo', 45)->unique();
            $table->string('description', 45);
            $table->integer('count');
            $table->integer('condition');
            $table->string('serialNo', 45)->unique();
            $table->integer('lendability');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2019_08_19_000000_create_rental_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventoryId');
            $table->string('name', 256);
            $table->string('adress', 256);
            $table->string('email', 45);
            $table->integer('deposit');
            $table->string('phone', 45);
            $table->date('borrowDate');
            $table->date('dueDate');
            $table->date('returnDate')->nullable();
            $table->string('comment', 256)->nullable();
            $table->unsignedBigInteger('lendingUser');
            $table->unsignedBigInteger('receivingUser')->nullable();

            $table->foreign('inventoryId')->references('id')->on('inventory');
            $table->foreign('lendingUser')->references('id')->on('user');
            $table->foreign('receivingUser')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental');
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_10_183606_add_image_and_price_to_inventory.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageAndPriceToInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('price');
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_11_172841_modify_description_column_in_table_name.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDescriptionColumnInTableName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('description', 255)->change();
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_11_173242_modify_name_column_in_inventory.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNameColumnInInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('inventoryNo')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('inventoryNo', 45)->change();
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_13_192103_add_phone_numbers_to_user_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneNumbersToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('phone_numbers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('phone_numbers');
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_13_192304_add_username_to_user_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('username')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_13_200921_add_status_to_rental_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToRentalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental', function (Blueprint $table) {
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
```

<div style="page-break-after: always;"></div>

### database/migrations/2024_12_19_200645_alter_password_column_to_hash.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class AlterPasswordColumnToHash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            // Modify the column to store hashed passwords (length is 255)
            $table->string('password', 255)->change();
        });

        // Optionally hash existing passwords if they are stored as raw text
        $users = \DB::table('user')->get();
        foreach ($users as $user) {
            \DB::table('user')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($user->password)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            // Revert the column to its original state (length is 45)
            $table->string('password', 45)->change();
        });
    }
}
```

