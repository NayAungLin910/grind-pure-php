<?php

namespace Src\Models;

class Course extends Model
{
    public static $table = "courses";

    public static $relationships = [
        "belongsTo" => [
            "user" => [
                "table" => "users",
                "foreign_id" => "user_id",
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
    ) {
        parent::__construct();
    }


}
