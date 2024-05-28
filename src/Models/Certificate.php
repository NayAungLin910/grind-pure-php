<?php

namespace Src\Models;

class Certificate extends Model
{
    public static $table = "certificates";

    public static $relationships = [
        "belongsToMany" => [ // many-to-many through pivot relationship
            "users" => [ // opposite relationship table
                "table" => "users",
                "pivot_table" => "certificate_user", // pivot table name
                "primary_key" => "id",
                "foreign_key" => "course_id",
                "other_table_primary_key" => "id", // opposite relationship pk
                "other_table_foreign_key" => "user_id", // opposite relationship fk
                "other_class" => User::class, // opposite relationship class model
            ]
        ]
    ];

    public function __construct(
        public int $id = 0,
        public int $course_id = 0,
        public string $title = "",
        public string $description = "",
        public string $image = "",
    ) {
        parent::__construct();
    }
}
