<?php
  
   require_once "global.php";
  
   $rs = $conexao->query("SELECT * FROM PPARAM WHERE CODCOLIGADA = 1");
  
   while($row = $rs->fetch(PDO::FETCH_OBJ)){
        echo "Competência: {$row->MESCOMP}";
   }

?>