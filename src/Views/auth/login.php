<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        <form action="/login" method="POST">
            <div>
                <label for="">Email</label>
                <input name="email" value="
                <?php
                
                if (isset($_SESSION["old"]["email"])) { // get the old value from session
                    echo htmlspecialchars($_SESSION["old"]["email"]);
                    unset($_SESSION["old"]["email"]);
                }
                ?>" type="email">
            </div>
            <div>
                <label for="">Password</label>
                <input name="password" type="password">
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>