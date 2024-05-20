<?php

namespace Src\Controllers;

use ArrayHelpers;
use Exception;
use Src\Controller;
use Src\DbConnection;
use Src\Models\User;


class UserController extends Controller
{
    public function index(): void
    {
        $this->render('user/index');
    }

    public function create(): void
    {
        $dbConnectionClass = new DbConnection();
        $dbConnection = $dbConnectionClass->getMySQLConnection();

        $getUserWithBookNamesQuery =  "SELECT users.username, books.name\n
         FROM users\n
         LEFT JOIN books\n
         ON users.id = books.user_id
         WHERE users.id = ?";

        if ($statement = $dbConnection->prepare($getUserWithBookNamesQuery)) {
            $userId = 2;
            $statement->bind_param("i", $userId);
            $statement->execute();

            if ($result = $statement->get_result()) {
                $resultUsers = [];

                while ($row = $result->fetch_assoc()) {

                    if (!ArrayHelpers::checkValueSameNestedArray($resultUsers, $row["username"], "username")) {
                        $resultUsers[] = [
                            "username" => $row["username"],
                            "books" => [
                                ["name" => $row["name"]]
                            ]
                        ];
                    } else {
                        foreach ($resultUsers as $index => $resultUser) {

                            if ($resultUser["username"] == $row["username"]) {
                                $resultUsers[$index]["books"][] = ["name" => $row["name"]];
                            }
                        }
                    }
                }

                dd($resultUsers);
            } else {
                throw new Exception("No result was fetced for the query, $getUserWithBookNamesQuery" . mysqli_error($dbConnection));
            }
        } else {
            throw new Exception("The preparation for the query $getUserWithBookNamesQuery" . mysqli_error($dbConnection));
        }
    }
}
