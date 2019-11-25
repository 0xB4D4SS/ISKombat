<?php
error_reporting(1);
require_once("application/Application.php");

function router($params) {
    $app = new Application();
    $method = $params["method"];
    switch ($method) {
        //user methods
        case "login": return $app->login($params);                              // логинит пользователя
        case "register": return $app->register($params);                        // регистрирует нового пользователя
        case "logout": return $app->logout($params);                            // разлогинивает пользователя
        // lobby
        case "getAllUsers": return $app->getAllUsers($params);                  // возвращает список пользователей онлайн
        case "isUserChallenged": return $app->isUserChallenged($params);        // возвращает истину, если пользователю уже отправили запрос на бой
        case "newChallenge": return $app->newChallenge($params);                // создает запись в БД, в lobby с новым запросом на бой
        case "isChallenge": return $app->isChallenge($params);                  // возвращает запись из БД из таблицы lobby, если пользователя вызывали на бой
        case "isChallengeAccepted": return $app->isChallengeAccepted($params);  // возвращает запись из БД из таблицы lobby, если вызываемый на бой пользователь принял вызов
        case "acceptChallenge": return $app->acceptChallenge($params);          // обновляет статус записи в БД в таблице lobby в зависимости от того, принял ли пользователь вызов на бой 
        // game methods
        case "move": return $app->move($params);           // метод, перемещающий бойца по сцене
        //case "setState": return $app->setState($params); // метод, меняющий состояние бойца ( стоит, сидит, лежит, в прыжке, мертв )
        case "hit": return $app->hit($params);             // метод, позволяющий бойцу сделать удар ( ногой или рукой )
        case "deleteFighter": return $app->deleteFighter($params);
        case "update": return $app->update($params);       // метод, обновляющий данные битвы
        default: return false;
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
