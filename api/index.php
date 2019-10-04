<?php
error_reporting(-1);
require_once("application/Application.php");

//testing GET, POST etc.
function router($params) {
    $app = new Application();
    $method = $params["method"];
    switch ($method) {
        case "test": return $app->test($params);
        case "move": return $app->move($params);
        default:     return false;
    }  
}

function answer($data) {
    if ($data) {
        return array(
            "result" => "ok",
            "data" => $data
            );
    }
    return array(
        "result" => "error",
        "error" => array(
                "code" => 999,
                "text" => "unknown error"
                )
        );
}

echo json_encode(answer(router($_GET)));
