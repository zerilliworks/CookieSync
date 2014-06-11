<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Cartalyst\Attributes\Entity;
use Illuminate\Support\Contracts\JsonableInterface;

class User extends Entity implements UserInterface, RemindableInterface, JsonableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

    protected $guarded = array(
        'id',
        'created_at',
        'updated_at',
    );

    public static function boot()
    {
        parent::boot();
    }

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

    public function saves()
    {
        return $this->hasMany('Save')->orderBy('created_at', 'desc');
    }

    public function games()
    {
        return $this->hasMany('Game');
    }

    public function latestSave()
    {
        return (!empty($this->saves[0])) ? $this->saves[0] : false;
    }

    public function addSave($data)
    {
        return $this->saves()->save(new Save(array('save_data' => $data)));
    }

    public function toJson($options = 0)
    {
        return json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
                           ], $options);
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
