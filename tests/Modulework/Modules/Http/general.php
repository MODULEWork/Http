<?php

use Modulework\Modules\Http\Request;

echo "General Testing<br />", PHP_EOL ;


require '../../../../vendor/autoload.php';

$req = Request::makeFromGlobals();

echo $req;

echo "<hr>", PHP_EOL;
var_dump($req);
echo "<hr>", PHP_EOL;