<?php

// Abre uma conexao com o BD.

//$host        = "host = 127.0.0.1";
//$port        = "port = 6649";
//$dbname      = "dbname = pg_products";
//$credentials = "user = postgres password=0000000";

//$db_con = pg_connect( "$host $port $dbname $credentials"  );

$db_con = pg_connect(getenv("DATABASE_URL"));
?>