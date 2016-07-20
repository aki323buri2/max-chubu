<?php
require_once __dir__.'/../php/functions.php';
$input = array_merge($_GET, $_POST);
$tokusc = nv(@$input['tokusc']);

sleep(1);

echo $tokusc.' is downloaded!!!';