<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
</head>

<body>
    <div>
        <?php

        session_start();

        if (isset($_SESSION["errors"])) { // if errror session exits
            var_dump($_SESSION["errors"]);  
            unset($_SESSION["errors"]);
        }
        ?>
        <form action="/register" method="POST">
            <div>
                <label for="">Name</label>
                <input name="name" type="text">
            </div>
            <div>
                <label for="">Email</label>
                <input name="email" type="email">
            </div>
            <div>
                <label for="">Password</label>
                <input name="password" type="password">
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>

</html>