<?php

use Modulework\Modules\Http\Request;

echo "General Testing<br />", PHP_EOL ;


$http_package = dirname(str_replace('tests', 'src', __FILE__)) . DIRECTORY_SEPARATOR;

include $http_package . 'ArrayCaseInterface.php';
include $http_package . 'ArrayCase.php';
include $http_package . 'FileCase.php';
include $http_package . 'ServerCase.php';
include $http_package . 'Request.php';

$req = Request::makeFromGlobals();

echo $req;

echo '<hr>', PHP_EOL;
var_dump($req);
