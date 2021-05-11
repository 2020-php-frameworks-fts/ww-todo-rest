<?php

if(isset($_SERVER["HTTP_ORIGIN"]))
{
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
else
{
    //No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); //Make sure you remove those you do not want to support

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    //Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}

$task_name = $_POST["name"];
$user_id = $_POST["user_id"];
$query = "DELETE FROM task WHERE name = \"{$task_name}\" AND user_id = \"{$user_id}\"";

if($task_name == null && $user_id == null) {
    http_response_code(500);
    die();
}

$mysqlConnection = new mysqli("host","login","password","database");

if($mysqlConnection -> connect_errno) {
    http_response_code(501);
    die();
}

$result = $mysqlConnection->query($query);

if(!$result) {
    http_response_code(502);
    echo($mysqlConnection->error);
    $mysqlConnection->close();
}

$mysqlConnection->close();
http_response_code(200);