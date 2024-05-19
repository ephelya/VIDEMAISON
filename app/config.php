<?php
    $host = 'http://videmaison.nathaliebrigitte.com/';


    $db   = $_ENV['DB'] ?? getenv('DB');
    $user = $_ENV['USER'] ?? getenv('USER');
    $pass =  $_ENV['PASS'] ?? getenv('PASS');
    $charset =  $_ENV['CHARSET'] ?? getenv('CHARSET');

    $development = true;


