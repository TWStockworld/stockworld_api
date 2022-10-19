<?php
echo 'Memory in use: ('. (memory_get_usage(true)/1024)/1024 .'M) <br>';
echo 'Peak usage:  ('. (memory_get_peak_usage(true)/1024)/1024 .'M) <br>';
echo 'Memory limit: ' . ini_get('memory_limit') . '<br>';
phpinfo();

?>