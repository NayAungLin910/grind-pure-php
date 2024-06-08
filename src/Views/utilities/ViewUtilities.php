<?php

//------------------------------- View Utilities -------------------------------


//------------------------------- Functions ------------------------------------

use Src\Services\CookieService;

/**
 * If the session has the given value, display it
 */
function displayFlashedSessionValue(string $sessionName, string $key): void
{
    if (isset($_SESSION[$sessionName][$key])) {
        echo htmlspecialchars($_SESSION[$sessionName][$key]);
        unset($_SESSION[$sessionName][$key]);
    }
}

/**
 * If error message(s) for a field, display message(s)
 */
function displayErrorMessage(string $field): void
{
    if (isset($_SESSION["errors"][$field]) && count($_SESSION["errors"][$field]) > 0) {

        echo '<span class="error-message">' . htmlspecialchars($_SESSION["errors"][$field][0]) . '</span>';
        unset($_SESSION["errors"][$field]);
    }
}

/**
 * Display all error message(s)
 */
function displayAllErrorMessages(): void
{
    if (isset($_SESSION["errors"]) && count($_SESSION["errors"]) > 0) {

        foreach ($_SESSION["errors"] as $fieldName => $messages) {

            if (is_array($_SESSION["errors"][$fieldName]) && count($_SESSION["errors"][$fieldName]) > 0) {

                echo '<span class="error-message">' . htmlspecialchars($_SESSION["errors"][$fieldName][0]) . '</span>';
                unset($_SESSION["errors"][$fieldName]);
            }
        }
    }
}

/**
 * Get route using route name
 */
function getRouteUsingRouteName(string $routeName): string
{
    if ($routeName == "") {
        throw new Exception("Please enter a route name");
    }

    include "../src/routes.php";

    foreach ($router->routes as $url => $mappedInfos) {
        foreach ($router->routes[$url] as $method => $value) {

            $routeNameExists = isset($router->routes[$url][$method]["name"]); // the route name parameter is defined
            if (!$routeNameExists) continue;

            $routeNameSame = $router->routes[$url][$method]["name"] == $routeName; // check if the current route name is the same with the route being searched 

            if ($routeNameSame) {
                return $url; // return the route
            }
        }
    }

    throw new Exception("$routeName is not a declared route name.");
}

//------------------------------- If condition boolean variables  ------------------------------------

$cookieService = new CookieService();

$ifAuth = isset($_SESSION["auth"]) || $cookieService->confirmRememberLoginCookie();

$ifAuthUser = $ifAuth && $_SESSION["auth"]["role"] == "user"; // if auth user

$ifAuthAdmin = $ifAuth && $_SESSION["auth"]["role"] == "admin"; // if auth admin
