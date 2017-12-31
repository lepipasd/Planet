<?php

function redirect_to($location = null)
{
    if ($location != null) {
        header("Location: {$location}");
        exit;
    }
}

function output_message($message = "")
{
    if (!empty($message)) {
        return $message;
    } else {
        return "";
    }
}

function __autoload($class_name)
{
    $class_name = strtolower($class_name);
    $path = LIB_PATH.DS."{$class_name}.php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        die("The file {$class_name}.php could not be found.");
    }
}

function password_encrypt($password)
{

    // use blowfish with a "cost" of 10
    $hash_format = "$2y$10$";
    // Blowfish salt should be 22-characters or more
    $salt_length = 22;

    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
}

function generate_salt($length)
{

    // Not 100% unique, not 100% random, but good enough for a salt
    // MD5 returns 32 characters
    $unique_random_string = md5(uniqid(mt_rand(), true));

    // valid characters for a salt are [a-zA-Z0-9./]
    $base64_string = base64_encode($unique_random_string);

    // But not '+' which is valid in base64 encoding
    $modified_base64_string = str_replace('+', '.', $base64_string);

    // Truncate string to the correct length
    $salt = substr($modified_base64_string, 0, $length);

    return $salt;
}

function password_check($password, $existing_hash)
{

    // existing hash contains format and salt at start
    $hash = crypt($password, $existing_hash);
    if ($hash === $existing_hash) {
        return true;
    } else {
        return false;
    }
}

function has_presence($value)
{
    $trimmed_value = trim($value);
    return isset($trimmed_value) && $trimmed_value != "";
}

function has_length($value, $options = [])
{
    if (isset($options['max']) && (strlen($value) > (int)$options['max'])) {
        return false;
    }
    if (isset($options['min']) && (strlen($value) < (int)$options['min'])) {
        return false;
    }
    if (isset($options['exact']) && (strlen($value) != (int)$options['exact'])) {
        return false;
    }
    return true;
}

function has_format_matching($value, $regex = '//')
{
    return preg_match($regex, $value);
}

/**//**
     * Description $options: max, min
     *             has_number($value, ['min' => 20, 'max' => 40]),
     * @return true or false
     */
function has_number($value, $options = [])
{
    if (!is_numeric($value)) {
        return false;
    }

    if (isset($options['max']) && ($value > (int)$options['max'])) {
        return false;
    }

    if (isset($options['min']) && ($value < (int)$options['min'])) {
        return false;
    }

    if (isset($options['exact']) && ($value != (int)$options['exact'])) {
        return false;
    }

    return true;
}

/**//**
 * Description validate inclusion in a set
 * @return true or false
 */
function has_inclusion_in($value, $set = [])
{
    return in_array($value, $set);
}

/**//**
 * Description validate exclusion from a set
 * @return true or false
 */
function has_exclusion_from($value, $set = [])
{
    return !in_array($value, $set);
}

/**//**
 * Description logger
 * @return void
 */
function logger($level = "ERROR", $msg = "")
{
    $log_file = SITE_ROOT.DS.'errors.log';

    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());

    $log_message  = "[";
    $log_message .= $timestamp;
    $log_message .= " EUROPE/ATHENS] ";
    $log_message .= $level;
    $log_message .= ": ";
    $log_message .= $msg;
    $log_message .= PHP_EOL;

    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

/**//**
 * Description sanitize for HTML output
 * @return sanitized data
 */
function h($string)
{
    return htmlspecialchars($string);
}

/**//**
 * Description sanitize for Javascript output
 * @return sanitized data
 */
function j($string)
{
    return json_encode($string);
}

/**//**
 * Description sanitize for use in a url
 * @return sanitized data
 */
function u($string)
{
    return urlencode($string);
}

/**//**
 * Description sanitize for use in a url
 * @return sanitized data
 */
function request_is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**//**
 * Description sanitize for use in a url
 * @return sanitized data
 */
function request_is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**//**
 * Description use with request_is_post
 * to block posting from off-site forms
 * @return true or false
 */
function request_is_same_domain()
{
    if (!isset($_SERVER['HTTP_REFERER'])) {
        // No referer sent, so can't be same domain
        return false;
    } else {
        $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        $server_host = $_SERVER['HTTP_HOST'];

        return ($referer_host == $server_host) ? true : false;
    }
}
