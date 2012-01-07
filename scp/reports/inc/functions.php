<?php

// This file contains all the functions in alphabetical order.
// 

/* this function simply takes dates in MM/DD/YYYY format and changes it to
   YYYY/MM/DD format which is what MySQL wants the DATE datatype to be */	 
function dateToDB($date) 
 {
 $d = explode('/',$date);
 $dbdate = $d[2]."-".$d[0]."-".$d[1];
 return($dbdate);
 }


 
/* this function simply takes dates in YYYY/MM/DD format and changes it to
   MM/DD/YYYY format */
function dateToNorm($date)
 { 
 $d = explode('-',$date);
 $displaydate = $d[1]."/".$d[2]."/".$d[0];
 return($displaydate);
 }

 
 
/*  When you call anyone of the two functions, set the $_str
     variable to the string that you want to encode or decode */

/* This function encodes the string.
    You can safetly use this function to save its result in a
    database. It eliminates any space in the beginning ou end
    of the string, HTML and PHP tags, and encode any special
    char to the usual HTML entities (&[...];), eliminating the
    possibility of bugs in inserting data on a table */
function encodeText($_str) {
  $_str = strip_tags($_str);
  $_str = trim($_str);
  $_str = htmlentities($_str);
  $_str = str_replace("\r\n", "#BR#", $_str);
  return($_str);
}

/* This function decodes the string.
    If you are showing the string in the body of a page, you
    can set the $_form variable to "false", and the function will
    use the "BR" tag to the new lines. But, if you need to show
    the string in a textarea, text or other input types of a form
    set the $_form variable to "true", then the function will use
    the "\r\n" to the new lines */
function decodeText($_str, $_form) {
  $trans_tbl = get_html_translation_table (HTML_ENTITIES);
  $trans_tbl = array_flip ($trans_tbl);
  $_str      = strtr($_str, $trans_tbl);
  if ($_form) {
    $_nl = "\r\n";
  } else {
    $_nl = "<br>";
  }
  $_str      = str_replace("#BR#", "$_nl", $_str);
  return($_str);
}


// function to determine time
function duration($secs) {

 $vals = array('w' => (int) ($secs / 86400 / 7),
  'd' => $secs / 86400 % 7,
  'h' => $secs / 3600 % 24,
  'm' => $secs / 60 % 60,
  's' => $secs % 60);
 
 $ret = array();
 
 $added = false;
 foreach ($vals as $k => $v) {
  if ($v > 0 || $added) {
   $added = true;
   $ret[] = $v . $k;
  }
 }

return join(' ', $ret);
} 

?>