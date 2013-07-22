<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be run in a browser and with a Webserver (php -S is just fine)
 */
use Modulework\Modules\Http\Request;

echo "General Testing<br />", PHP_EOL ;

require '../../../../vendor/autoload.php';

$req = Request::makeFromGlobals();

echo $req;

echo "<hr>", PHP_EOL;
var_dump($_SERVER);
echo "<hr>", PHP_EOL;


$request = new Request;
$request->init(array('FOO'), array('FS'), array(), array('F'), array('F'));

var_dump($request);