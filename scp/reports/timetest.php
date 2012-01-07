<?
$sunday = date(('Y-m-d H:i:s'), strtotime('last sunday 09:00'));

echo "$sunday<br><br>";

$previous = date(('Y-m-d H:i:s'), strtotime('2 weeks ago 09:00'));

echo "$previous<br><br>";
