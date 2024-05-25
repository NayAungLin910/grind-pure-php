<?php

namespace Src\Models;

class User extends Model
{
    public static $table = "users"; // set table name related to the model user

    public function __construct(
        public int $id = 0,
        public string $name = "",
        public string $email = "",
        public string $profile_image = "",
        public string $role = "user",
        public string $created_at = "",
        public string|null $updated_at = ""
    ) {
        parent::__construct();
    }
}
