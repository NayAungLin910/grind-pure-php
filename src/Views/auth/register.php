<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
</head>

<body>
    <div>
        <form action="/register">
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