<?php

namespace Src\Models;

class User extends Model
{
    protected static $table = "users"; // set table name related to the model user

    public function __construct(public int $id = 0, public string $name = "", public string $email = "", public string $profile_image = "", public string $role = "user")
    {
        parent::__construct();
    }
}
