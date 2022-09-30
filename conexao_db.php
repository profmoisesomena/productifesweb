<?php

// Abre uma conexao com o BD.

//$host        = "host = ${{ PGHOST }}";
//$port        = "port = ${{ PGPORT }}";
//$dbname      = "dbname = PGDATABASE";
//$credentials = "user = ${{ PGUSER }} password=${{ PGPASSWORD }}";
//postgresql://${{ PGUSER }}:${{ PGPASSWORD }}@${{ PGHOST }}:${{ PGPORT }}/${{ PGDATABASE }}
//$db_con = pg_connect( "$host $port $dbname $credentials"  );

$db_con = pg_connect(getenv("dabase_elephant"));
?>