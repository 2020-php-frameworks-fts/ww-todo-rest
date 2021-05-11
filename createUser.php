<?php
// 201 - empty data
// 202 - mysql connection err
// 203 - found account
// 204 - something with insert
// 200 successfull
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

$login = $_POST["login"];
$password = $_POST["password"];

// query check if user exists
$query_check = "SELECT login FROM users WHERE login = \"${login}\"";

// query creating new user
$query_insert = "INSERT INTO users (login, password) VALUES(\"{$login}\",\"{$password}\");";

if($login == null && $password == null) {
    http_response_code(201);
    die();
}

$mysqlConnection = new mysqli("host","login","password","database");

if($mysqlConnection -> connect_errno) {
    echo("failed");
    http_response_code(202);
    die();
}

$result = $mysqlConnection->query($query_check);
$data = $result->fetch_array(MYSQLI_NUM);

if ($data != null) {
    if(count($data) > 0) {
        http_response_code(203);
        $mysqlConnection->close();
        die();
    }
}
$result_insert = $mysqlConnection->query($query_insert);

if($result_insert != null) {
    if($result_insert == false) {
        http_response_code(204);
        $mysqlConnection->close();
        die();
    }
} else {
    http_response_code(204);
    echo($mysqlConnection->error);
    $mysqlConnection->close();
    die();
}

$mysqlConnection->close();
http_response_code(200);
echo($login);
?>