<?php

namespace Src\Models;

class User extends Model
{
    public static $table = "users";

    public static $relationships = [
        "hasMany" => [
            "courses" => [
                "table" => "courses",
                "foreign_id" => "user_id",
                "class" => Course::class,
            ],
        ],
    ];

    public function __construct(
        public int $id = 0,
        public string $name = "",
        public string $email = "",
        public string $profile_image = "",
        public string $role = "user",
        public array $courses = [],
    ) {
        parent::__construct();
    }
}
