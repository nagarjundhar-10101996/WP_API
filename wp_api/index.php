<?php
declare(strict_types=1);
header("Content-type: application/json; charset=UTF-8");
//autoload class using file as required 
spl_autoload_register(function ($class) {
    $spl_mark = strpos($class, '_') ?? false;
    if($spl_mark)
    {
        $SPLCLASS=(array_combine(['File','Part'],explode('_',strval($class))) );
        require __DIR__ . "/v1/{$SPLCLASS['Part']}/$class.php";
    }
    else
    {
        require __DIR__ . "/v1/$class.php";
    }
});
set_exception_handler("ErrorHandler::handleException");
// url request:
$url_part = explode('/',$_SERVER['REQUEST_URI']);
//test web client without curl
//new Token('4ed31d41a135204be4');
if ($url_part[1] != "wp_api") {
   
    http_response_code(404);
    exit;
}
else if(!empty($_SERVER['HTTP_AUTHORIZATION'])&& $url_part[2] == 'v1')
{
    
    //echo $_SERVER['HTTP_AUTHORIZATION'];
    $token = Token::Sign(['id' => 'demoid'], $_SERVER['HTTP_AUTHORIZATION'], 60*5);
    // Vefity token
    //$payload = Token::Verify($token, $_SERVER['HTTP_AUTHORIZATION']);
    //print_r(empty($_SERVER['HTTP_AUTHORIZATION']));
    //print_r($payload);
    //print_r($_SERVER);
    //print_r(array_splice($url_part, 3, 5));
    $database = new Database("localhost", "posts", "root", "");
    $model = new posts_model($database);
    $controller = new posts_controller($model);
    $controller->procreq($_SERVER["REQUEST_METHOD"],array_splice($url_part, 3, (count($url_part))));
}
else
{
    http_response_code(404);
    exit;
}


?>