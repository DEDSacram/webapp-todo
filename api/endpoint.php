<?php
function get_method() {
    return $_SERVER['REQUEST_METHOD'];
}

function get_request_data() {
    $requestData = array_merge(
        empty($_POST) ? array() : $_POST,
        (array) json_decode(file_get_contents('php://input'), true),
        $_GET
    );

    // Translate key values to arrays if necessary
    // foreach ($requestData as $key => $value) {
    //     if (is_string($value) && strpos($value, ',') !== false) {
    //         $requestData[$key] = explode(',', $value);
    //     }
    // }

    return $requestData;
}

function send_response($response, $code = 200) {
    http_response_code($code);
    die(json_encode($response));
}

function is_not_ajax() {
    return empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest';
}