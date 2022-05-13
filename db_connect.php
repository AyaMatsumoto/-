<?php

//---------------------------------------------------
// DB接続用
//---------------------------------------------------
function getPdb()
{
  $dbname   = "dbname";
  $host     = "mysql.phy.lolipop.jp";
  $dsn      = "mysql:dbname={$dbname};host={$host};charset=utf8mb4";
  $user     = "user";
  $pass     = "pass";
  $options  = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);

  return new PDO($dsn, $user, $pass, $options);
}
