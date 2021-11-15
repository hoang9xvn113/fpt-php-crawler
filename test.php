<?php

$file = fopen("selenium/my_cookie.json", "r");
$cookies = json_decode(fread($file, filesize("selenium/my_cookie.json")));

print_r($cookies);
