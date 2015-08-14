<?php namespace App\Models;

use \Hash;
use \Carbon\Carbon;
use \Illuminate\Auth\Authenticatable;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Auth\Passwords\CanResetPassword;
use \Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use \Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * @property int    id
 * @property string name
 * @property string email
 * @property string password
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @method static User      findOrFail(int $id)
 * @method static null|User find(int $id)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];


    /**
     * Set password. The password would be hashed.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make((string)$value);
    }
}
