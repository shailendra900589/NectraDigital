<?php
mysqli_report(MYSQLI_REPORT_OFF);
foreach (['127.0.0.1', 'localhost'] as $host) {
    $c = @new mysqli($host, 'root', '', 'nectradigital');
    echo $host . ': ' . ($c->connect_error ?: 'OK') . PHP_EOL;
    if ($c && !$c->connect_error) {
        $c->close();
    }
}
