<?php

namespace Src\Models;

class User extends AuthModel
{
    public static $table = "users";

    public static $relationships = [
        "hasMany" => [ // one-to-many relationship
            "courses" => [
                "table" => "courses", // opposite relationship table
                "primary_id" => "id",
                "foreign_id" => "user_id",
                "class" => Course::class,
            ],
        ],
        "belongsToMany" => [ // many-to-many through pivot relationship
            "certificates" => [ // opposite relationship table
                "other_table" => "certificates",
                "pivot_table" => "certificate_user", // pivot table name
                "primary_key" => "id",
                "foreign_key" => "user_id",
                "other_table_primary_key" => "id", // opposite relationship pk
                "other_table_foreign_key" => "certificate_id", // opposite relationship fk
                "class" => Certificate::class, // opposite relationship class model
            ]
        ]
    ];

    public function __construct(
        public int $id = 0,
        public string $name = "",
        public string $email = "",
        public string $profile_image = "",
        public string $role = "user",
        public array $courses = [],
        public array $certificates = [],
        public string $password = "",   
    ) {
        parent::__construct();
    }
}
