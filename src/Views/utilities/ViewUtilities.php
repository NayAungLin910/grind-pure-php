<?php

//------------------------------- View Utilities -------------------------------


//------------------------------- Functions ------------------------------------

use Doctrine\Common\Collections\Collection;
use Src\Models\Course;
use Src\Models\Step;
use Src\Models\User;
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
 * If error message(s) for a field, display message(s)
 */
function displaySuccessMessage(string $field): void
{
    if (isset($_SESSION["errors"][$field]) && count($_SESSION["errors"][$field]) > 0) {

        echo '<span class="success-message">' . htmlspecialchars($_SESSION["errors"][$field][0]) . '</span>';
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
 * Check if the current route contains 
 * the given string
 */
function checkCurrentRouteContains(string $checkRoute)
{
    return str_contains($_SERVER['REQUEST_URI'], $checkRoute);
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

/**
 * Echo the test if the id is in the given collection
 */
function checkIdInCollection(Collection $collection, int $id): bool
{
    foreach ($collection as $c) {
        if ($c->getId() === $id) return true;
    }

    return false;
}

/**
 * Get authenticated user
 */
function getAuthUser(): User|null
{
    require "../config/bootstrap.php";

    if (!isset($_SESSION['auth']['id'])) return null;

    $user = $entityManager->createQueryBuilder()
        ->select('u')
        ->from(User::class, "u")
        ->where('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
        ->getQuery()
        ->getOneOrNullResult();

    if (!$user) return null;

    return $user;
}

/**
 * Check if course is completed
 */
function checkCourseCompleted(Course $course): bool
{
    require "../config/bootstrap.php";

    $allStepsCount = $entityManager->createQueryBuilder()
        ->select('count(s.id)')
        ->from(Step::class, 's')
        ->leftJoin('s.section', 'se')
        ->leftJoin('se.course', 'c')
        ->where('c.id = :c_id')->setParameter('c_id', $course->getId())
        ->getQuery()
        ->getSingleScalarResult();

    $allCompletedStepsByUser = $entityManager->createQueryBuilder()
        ->select('count(s.id)')
        ->from(Step::class, 's')
        ->leftJoin('s.section', 'se')
        ->leftJoin('se.course', 'c')
        ->leftJoin('s.users', 'u')
        ->where('c.id = :c_id')->setParameter('c_id', $course->getId())
        ->andWhere('u.id = :u_id')->setParameter('u_id', $_SESSION['auth']['id'])
        ->getQuery()
        ->getSingleScalarResult();

    if ($allStepsCount == $allCompletedStepsByUser) return true;

    return false;
}

//------------------------------- If condition boolean variables  ------------------------------------

$ifAuth = isset($_SESSION["auth"]);

$ifAuthUser = $ifAuth && $_SESSION["auth"]["role"] == "user"; // if auth user

$ifAuthAdmin = $ifAuth && $_SESSION["auth"]["role"] == "admin"; // if auth admin
