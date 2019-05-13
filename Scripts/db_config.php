<?php
   
   $dbhost = 'localhost:3306';
   $dbuser = 'phpmyadmin';
   $dbpass = 'rabbit@1531';
   $dbname = 'Sysmon_Events';
   //$conn = mysql_connect($dbhost, $dbuser, $dbpass,  $dbname);
   $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
   
   if(! $conn ) {
      die('Could not connect: ' . mysql_error());
   }
  
   //mysql_close($conn);
?>