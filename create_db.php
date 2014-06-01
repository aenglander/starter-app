#!/usr/bin/php
<?php
if (!$argc) die('This file is not viewable from the web');

$pdo = new PDO('sqlite:' . __DIR__ . '/registry.sq3');
$query = 'create table if not exists registrants(first TEXT, last TEXT, registered_timestamp INTEGER);';

$success = $pdo->exec($query);

if ($success !== false) {
    print "Database create command executed successfully\n";
} else {
    sprintf(
        "Database create command did not successfully complete.  Error:\n%s",
        print_r($pdo->errorInfo(), true)
    );
}
