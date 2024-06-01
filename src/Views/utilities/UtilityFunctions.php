<?php

/**
 * Utilitiy functions to be used in php views
 */

/**
 * If the session has the given value, display it
 */
function displayFlashedSessionValue(string $sessionName, string $key): void
{
    session_start();

    if (isset($_SESSION[$sessionName][$key])) {
        echo htmlspecialchars($_SESSION[$sessionName][$key]);
        unset($_SESSION[$sessionName][$key]);
    }

    session_write_close();
}

/**
 * If error message(s) for a field, display message(s)
 */
function displayErrorMessage(string $field): void
{
    session_start();

    if (isset($_SESSION["errors"][$field]) && count($_SESSION["errors"][$field]) > 0) {

        echo '<span class="error-message">' . htmlspecialchars($_SESSION["errors"][$field][0]) . '</span>';
        unset($_SESSION["errors"][$field]);
    }

    session_write_close();
}

/**
 * Display all error message(s)
 */
function displayAllErrorMessages(): void
{
    session_start();

    if (isset($_SESSION["errors"]) && count($_SESSION["errors"]) > 0) {

        foreach ($_SESSION["errors"] as $fieldName => $messages) {

            if (is_array($_SESSION["errors"][$fieldName]) && count($_SESSION["errors"][$fieldName]) > 0) {

                echo '<span class="error-message">' . htmlspecialchars($_SESSION["errors"][$fieldName][0]) . '</span>';
                unset($_SESSION["errors"][$fieldName]);
            }
        }
    }

    session_write_close();
}
