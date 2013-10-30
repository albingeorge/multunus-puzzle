<?php

class TwitterModel
{
  private $db_name;
  private $db_user;
  private $db_pass;
  private $host;
  private $table;
  private $con;

  // No separate config file for database, sincle there is only 1 table
  function __construct($db_name, $db_user, $db_pass, $host, $table) {
    $this->db_name = $db_name;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->host = $host;
    $this->table = $table;
    $this->con = mysql_connect($host, $db_user, $db_pass);
    mysql_select_db($db_name);
  }

  function __destruct() {
    mysql_close($this->con);
  }

  public function getJSON($handle){
    $res = mysql_query("select top 1 from $this->table where handle = '$handle'");
    if(mysql_num_rows($res) > 0){
      $row = mysql_fetch_assoc($res);
      return json_decode($row['json']);
    }
    return false;
  }

  public function setJSON($handle, $json) {
    if(!mysql_query("update $this->table set json = '" . mysql_real_escape_string($json) . "'' where handle = '$handle'")) {
      return false;
    }
  }
}