<?

$query = "select DATE_FORMAT(created, '%Y-%b') as partdate, COUNT(created) as counted FROM ost_ticket GROUP BY 'partdate' ORDER BY YEAR(created) DESC, MONTH(created) DESC LIMIT 0,18;";
$result=mysql_query($query);

$runningtemp = 0;

$values = array();
while($row = mysql_fetch_assoc($result)) {	
          //$temp = "".$row['month']."-".$row['year']."";
					//echo $temp."<br>";
					$values[$row['partdate']] = $row['counted'];
					//$runningtot = $runningtemp + $row['counted'];
					}

$runningtot = array_sum($values);					
										
// start bar graph code
// image definitions
	$img_width=800;
	$img_height=300; 
	$margins=25;


	# ---- Find the size of graph by substracting the size of borders
	$graph_width=$img_width - $margins * 2;
	$graph_height=$img_height - $margins * 2; 
	$img=imagecreate($img_width,$img_height);

 
	$bar_width=20;
	$total_bars=count($values);
	$gap= ($graph_width- $total_bars * $bar_width ) / ($total_bars +1);

 
	# -------  Define Colors ----------------
	$bar_color=imagecolorallocate($img,0,64,128);
	$background_color=imagecolorallocate($img,240,240,255);
	$border_color=imagecolorallocate($img,200,200,200);
	$line_color=imagecolorallocate($img,220,220,220);
 
	# ------ Create the border around the graph ------

	imagefilledrectangle($img,1,1,$img_width-2,$img_height-2,$border_color);
	imagefilledrectangle($img,$margins,$margins,$img_width-1-$margins,$img_height-1-$margins,$background_color);

 
	# ------- Max value is required to adjust the scale	-------
	$max_value=max($values)+2;
	$ratio= $graph_height/$max_value;

 
	# -------- Create scale and draw horizontal lines  --------
	$horizontal_lines=20;
	$horizontal_gap=$graph_height/$horizontal_lines;

	for($i=1;$i<=$horizontal_lines;$i++){
		$y=$img_height - $margins - $horizontal_gap * $i ;
		imageline($img,$margins,$y,$img_width-$margins,$y,$line_color);
		$v=intval($horizontal_gap * $i /$ratio);
		imagestring($img,0,5,$y-5,$v,$bar_color);

	}
 
  
	// remove this for test
	$total = "Total Tickets Monthly for all time: $runningtot";
	
	# ----------- Draw the bars here ------
	for($i=0;$i< $total_bars; $i++){ 
		# ------ Extract key and value pair from the current pointer position
		list($key,$value)=each($values); 
		$x1= $margins + $gap + $i * ($gap+$bar_width) ;
		$x2= $x1 + $bar_width; 
		$y1=$margins +$graph_height- intval($value * $ratio) ;
		$y2=$img_height-$margins;
		imagestring($img,0,$x1+3,$y1-10,$value,$bar_color);
		// process $key to make it smaller and easier to read
		$d = explode('-',$key);
		// only want last two digits of year for display.
		$temp = $d[1].substr($d[0],2,4);
		// add the key across the bottom of the graph
		imagestring($img,0,$x1+3,$img_height-22,$temp,$bar_color);		
		imagefilledrectangle($img,$x1,$y1,$x2,$y2,$bar_color);
	}
	
			// add total to the bottom
		imagestring($img,2,130,$img_height-15,$total,$bar_color);

	$temp_chart_file_name = "./reports/tickets_monthly.png"; 
	imagepng($img, $temp_chart_file_name,0);
	// since this is an include get rid of $values
  unset($values)
?>
