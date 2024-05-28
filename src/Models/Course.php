<?php

namespace Src\Models;

class Course extends Model
{
    public static $table = "courses";

    public static $relationships = [
        "belongsTo" => [ // one-to-many reverse relationship
            "user" => [
                "table" => "users",
                "primary_id" => "user_id",
                "foreign_id" => "id",
                "class" => User::class,
            ]
        ]
    ];

    public function __construct(
        public int $id = 0,
        public string $title = "",
        public string $description = "",
        public string $image = "",
        public int $user_id = 0,
        public array $user = [],
    ) {
        parent::__construct();
    }


}
