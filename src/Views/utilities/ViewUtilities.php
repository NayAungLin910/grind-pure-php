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
 * Check flash session exists
 */
function checkFlashedSessionExist(string $sessionName, string $key): bool
{
    if (isset($_SESSION[$sessionName][$key])) {
        unset($_SESSION[$sessionName][$key]);
        return true;
    }
    return false;
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

/**
 * Check if the route is the route mapped with the given routename
 */
function checkCurrentRouteSame(string $routeName): bool
{
    $route = getRouteUsingRouteName($routeName); // get the Route using route name

    $uri =  $_SERVER['REQUEST_URI'];

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && str_contains($_SERVER['REQUEST_URI'], '?')) { // if get method and the quesetion mark exists within the route
        $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')); // get the route before the question mark
    }

    return $uri === $route;
}

/**
 * echo class name if current route the same
 */
function echoClassCurrentRouteSame(string $routeName, string $className)
{
    if (checkCurrentRouteSame($routeName)) {
        echo $className;
    }
}

/**
 * Echo the text if old sessinon exist
 */
function checkIdExistsInOldSession(string $sessionKey, string $text, int $checkId): string|null
{
    if (isset($_SESSION['old'][$sessionKey]) && in_array($checkId, $_SESSION['old'][$sessionKey])) {

        $indexOfIdFound = array_search($checkId, $_SESSION['old'][$sessionKey]);
        unset($_SESSION['old'][$sessionKey][$indexOfIdFound]);

        return $text;
    }

    return null;
}
//------------------------------- If condition boolean variables  ------------------------------------

$cookieService = new CookieService();

$ifAuth = isset($_SESSION["auth"]);

$ifAuthUser = $ifAuth && $_SESSION["auth"]["role"] == "user"; // if auth user

$ifAuthAdmin = $ifAuth && $_SESSION["auth"]["role"] == "admin"; // if auth admin
