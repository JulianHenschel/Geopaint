<?php
class db {
  
 var $link_id  = 0;
 var $query_id = 0;
 var $record   = array();
 var $errdesc    = "";
 var $errno   = 0;
 var $version = "";
 var $show_error = 1;
   
 var $server   = PORT_DB_DATABASE_HOST;
 var $user     = PORT_DB_DATABASE_USERNAME;
 var $password = PORT_DB_DATABASE_PASSWORD;
 var $database = PORT_DB_DATABASE_NAME;
 
 var $use_unbuffered_query = false;

 var $appname  = "Content Management System";
  
 function db() {

  
  $password = "";
  
  // use mysql_unbuffered_query ? (need php >= 4.0.6)
  //if(version_compare($phpversion, "4.0.6")!=-1) $this->use_unbuffered_query=true;
  
  $this->connect();
  
  $this->password = "";
 }    
  
 function connect() {
  $this->link_id=@mysql_connect($this->server,$this->user,$this->password);
  if (!$this->link_id) $this->error("Link-ID == false, connect failed");
  if ($this->database!="") $this->select_db($this->database);
 }
 
 function geterrdesc() {
  $this->error=mysql_error();
  return $this->error;
 }

 function geterrno() {
  $this->errno=mysql_errno();
  return $this->errno;
 }
 
 function getversion() {
  if($this->link_id) list($this->version)=$this->query_first("SELECT VERSION()",0,0,MYSQL_BOTH,0);
  if(!$this->version) $this->version="unknown";
  return $this->version;
 }

 function select_db($database="") {
  if ($database!="") $this->database=$database;
  if(!@mysql_select_db($this->database, $this->link_id)) $this->error("cannot use database ".$this->database);
 }

 function query($query_string,$limit=0,$offset=0,$showerror=1) {
  if($limit!=0) $query_string.=" LIMIT $offset, $limit";
  $this->query_id = mysql_query($query_string,$this->link_id);
  if ($showerror==1 && !$this->query_id) $this->error("Invalid SQL: ".$query_string);
  return $this->query_id;
 }
 
 function unbuffered_query($query_string,$LOW_PRIORITY=0,$limit=0,$offset=0,$showerror=1) {
  if(!$this->use_unbuffered_query) return $this->query($query_string,$limit,$offset,$showerror);
  else {
   //if($LOW_PRIORITY==1) $query_string=preg_replace("/^(INSERT|UPDATE|DELETE|REPLACE)(.*)/si", "\\1 LOW_PRIORITY\\2", $query_string);
   if($limit!=0) $query_string.=" LIMIT $offset, $limit";
   $this->query_id = mysql_unbuffered_query($query_string,$this->link_id);
   if ($showerror==1 && !$this->query_id) $this->error("Invalid SQL: ".$query_string);
   return $this->query_id;
  }
 }

 function fetch_array($query_id=-1,$type=MYSQL_BOTH) {
  if ($query_id!=-1) $this->query_id=$query_id;
  $this->record = mysql_fetch_array($this->query_id, $type);
  return $this->record;
 }
 
 function fetch_row($query_id=-1) {
  if ($query_id!=-1) $this->query_id=$query_id;
  $this->record = mysql_fetch_row($this->query_id);
  return $this->record;
 }

 function query_first($query_string,$limit=0,$offset=0,$type=MYSQL_BOTH,$showerror=1) {
  $this->query($query_string,$limit,$offset,$showerror=1);
  $returnarray=$this->fetch_array($this->query_id,$type);
  return $returnarray;
 }

 function num_rows($query_id=-1) {
  if ($query_id!=-1) $this->query_id=$query_id;
  return mysql_num_rows($this->query_id);
 }
 
 function affected_rows() {
  return mysql_affected_rows($this->link_id);
 }
 
 function insert_id() {
  return mysql_insert_id($this->link_id);
 }

 function error($errormsg) {
  $this->errdesc=mysql_error();
  $this->errno=mysql_errno();
    		
  $errormsg="<b>Database error:</b> $errormsg\n<br>";
  $errormsg.="<b>mysql error:</b> $this->errdesc\n<br>";
  $errormsg.="<b>mysql error number:</b> $this->errno\n<br>";
  $errormsg.="<b>mysql version:</b> ".$this->getversion()."\n<br>";
  $errormsg.="<b>php version:</b> ".phpversion()."\n<br>";
  $errormsg.="<b>Date:</b> ".date("d.m.Y @ H:i")."\n<br>";
  $errormsg.="<b>Script:</b> ".getenv("REQUEST_URI")."\n<br>";
  $errormsg.="<b>Referer:</b> ".getenv("HTTP_REFERER")."\n<br><br>";

  if($this->show_error) $errormsg = "$errormsg";
  else $errormsg = "\n<!-- $errormsg -->\n";
  die("</table><font face=\"Verdana\" size=2><b>SQL-DATABASE ERROR</b><br><br>".$errormsg."</font>");
 }
 
 function data_seek($result,$nr)
 {
 	if(!mysql_data_seek($result,$nr)) $this->error("Can't seek to row $i in result");
 }
}
?>