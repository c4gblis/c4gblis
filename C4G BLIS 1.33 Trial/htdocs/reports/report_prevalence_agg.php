<?php

include("redirect.php");
include("includes/header.php");
include("includes/stats_lib.php");
LangUtil::setPageId("reports");

$script_elems->enableFlotBasic();
$script_elems->enableFlipV();
$script_elems->enableTableSorter();
$script_elems->enableLatencyRecord();
?>

<script type="text/javascript">
	$(window).load(function(){
		$('#stat_graph').hide();
		$('#stat_graph_bar').hide();
	});
		
	function showGraph() {
		$('#stat_graph').show();
		$('#stat_graph_bar').show();
		$('#viewGraphSpan').hide();
		$('#hideGraphSpan').show();
		$('#hideTrendSpan').hide();
		$('#viewTrendSpan').show();
	}
	
	function hideGraph() {
		$('#stat_graph').hide();
		$('#stat_graph_bar').hide();
		$('#viewGraphSpan').show();
		$('#hideGraphSpan').hide();
		$('#viewTrendSpan').show();
		$('#hideTrendSpan').hide();
	}
	
	function hideTrends() {
		$('#stat_graph').hide();
		$('#stat_graph_bar').hide();
		$('#viewGraphSpan').show();
		$('#hideGraphSpan').hide();
		$('#viewTrendSpan').show();
		$('#hideTrendSpan').hide();
		$('#trendsDiv').hide();
	}
	
	function viewTrendsIndividual(testName, labConfigIds, dateFrom, dateTo) {
		var url = "ajax/plotSpline.php?testName="+testName+"&labConfigIds="+labConfigIds+"&date_from="+dateFrom+"&date_to="+dateTo;
		$('#trendsDiv_progress_spinner').show();
		$('#trendsDiv').load(url);
		$('#trendsDiv_progress_spinner').hide();
	}
</script>

<?php
$test_type_id = $_REQUEST['testTypeCountry'];
$date_from = $_REQUEST['yyyy_from']."-".$_REQUEST['mm_from']."-".$_REQUEST['dd_from'];
$date_to = $_REQUEST['yyyy_to']."-".$_REQUEST['mm_to']."-".$_REQUEST['dd_to'];

$lab_config_id = $_REQUEST['locationAgg'];
$labNamesArray = array();
$xAxisGraph = array();
$progressTrendsData = array();
?>
<script type="text/javascript" src="js/highcharts.js"></script>
<br>
<div id = 'links'>
	<a href='reports.php?show_agg'>&laquo; <?php echo LangUtil::$pageTerms['MSG_BACKTOREPORTS']; ?></a> 
	<?php if ( !(($test_type_id == 0) && ($lab_config_id == 0 || count($lab_config_id) > 1) )) { ?>
	| 
	<span id='viewTrendSpan'><a href='javascript:viewTrends();'>View Trends</a></span>
	<span id='hideTrendSpan' style='display:none;'><a href='javascript:hideTrends();'>Hide Trends</a></span>
	<?php } ?>
 </div>
<br>
<b>
<?php echo LangUtil::$pageTerms['MENU_INFECTIONSUMMARY']; ?></b>

<?php
function publishInfo ( $lab_config_id, $test_type_id ) {
	if( $test_type_id == 0 && $lab_config_id == 0 ) { # All Tests & All Labs
		echo "<b> for All Tests for All Labs</b>";
	}
	else if ( $test_type_id == 0 && count($lab_config_id) == 1 ) { # All Tests for a Single Lab
		$lab_config = LabConfig::getById($lab_config_id[0]);
		$labName = $lab_config->name;
		echo "<b> for All Tests for $labName</b>";
	}
	else if ( $test_type_id == 0 && count($lab_config_id) > 1 ) { # All Tests for more than one lab
		echo "<b> for All Tests for ";
		$labNames = "";
		foreach( $lab_config_id as $key) {
			$lab_config = LabConfig::getById($key);
			$labName = $lab_config->name;
			$labNames .= $labName.", ";
		}
		$labNames = substr($labNames, 0, strlen($labNames)-2);
		echo "$labNames</b>";
	}
	else {
		$saved_db = DbUtil::switchToGlobal();
		$user_id = $_SESSION['user_id'];
		$query = "select test_name from test_mapping where lab_id_test_id = '$test_type_id' AND user_id = $user_id LIMIT 1";
		$record = query_associative_one($query);
		$testName = $record['test_name'];
		DbUtil::switchRestore($saved_db);
		if ( $test_type_id != 0 && $lab_config_id == 0 ) { #Particular Test & All Labs
			echo "<b> for $testName for All Labs</b>";
		}
		else if ( $test_type_id !=0 && count($lab_config_id) == 1 ) {
			$lab_config = LabConfig::getById($lab_config_id[0]);
			$labName = $lab_config->name;
			echo "<b> for $testName for $labName</b>";
		}
		else {
			echo "<b> for $testName for ";
			$labNames = "";
			foreach( $lab_config_id as $key) {
				$lab_config = LabConfig::getById($key);
				$labName = $lab_config->name;
				$labNames .= $labName.", ";
			}
			$labNames = substr($labNames, 0, strlen($labNames)-2);
			echo "$labNames</b>";
		}
	}
}

function publishDates( $date_from, $date_to ) {
	echo "<br><br>";
	if($date_from == $date_to)
			echo LangUtil::$generalTerms['DATE'].": ".DateLib::mysqlToString($date_from)."<br>";
	else {
			echo LangUtil::$generalTerms['FROM_DATE'].": ".DateLib::mysqlToString($date_from);
			echo " | ";
			echo LangUtil::$generalTerms['TO_DATE'].": ".DateLib::mysqlToString($date_to)."<br>";
	}
	echo "<br>";
}

function processWeeklyTrends( $lab_config_id, $test_type_id, $date_from, $date_to, $test_name = null) {
	global $namesArray;
	global $stat_list;
	
	/* All Tests & All Labs */ 
	if ( $test_type_id == 0 && $lab_config_id == 0 ) { 
		/*To Do
		$site_list = get_site_list($_SESSION['user_id']);

		foreach($stat_list as $mainKey => $mainValue) {
			$testName = $mainkey;
			
			foreach( $site_list as $subKey => $subValue ) {
				$lab_config = LabConfig::getById($key);
			}
		}
		*/
	}
	/* All Tests for Single Lab */
	else if ( $test_type_id == 0 && count($lab_config_id) == 1 ) {
	
		$lab_config = LabConfig::getById($lab_config_id[0]);
		$test_type_list = get_discrete_value_test_types($lab_config);
			
		foreach($test_type_list as $test_type_id) {
			$namesArray[] = get_test_name_by_id($test_type_id, $lab_config_id[0]);
			getWeeklyStats($lab_config, $test_type_id, $date_from, $date_to);
		}
	}
	/* All Tests for Multiple Labs */
	else if ( $test_type_id == 0 && count($lab_config_id) > 1 ) {
		//To Do
	}	
	else {
		/* Build Array Map with Lab Id as Key and Test Id as corresponding Value */
		$labIdTestIds = explode(";",$test_type_id);
		$testIds = array();
		foreach( $labIdTestIds as $labIdTestId) {
				$labIdTestIdsSeparated = explode(":",$labIdTestId);
				$labId = $labIdTestIdsSeparated[0];
				$testId = $labIdTestIdsSeparated[1];
				$testIds[$labId] = $testId;
		}	
		
		/* Single Test for All Labs */
		if ( $test_type_id != 0 && $lab_config_id == 0 ) {
			$site_list = get_site_list($_SESSION['user_id']);

			foreach( $site_list as $key => $value) {
				$lab_config = LabConfig::getById($key);
				$test_type_id = $testIds[$lab_config->id];
				$namesArray[] = $lab_config->name;
				getWeeklyStats($lab_config, $test_type_id, $date_from, $date_to);
			}
		}
		/* Single Test for Single Lab */
		else if ( $test_type_id != 0 && count($lab_config_id) == 1 ) {
			$lab_config = LabConfig::getById($lab_config_id[0]);
			$test_type_id = $testIds[$lab_config->id];
			$namesArray[] = $lab_config->name;
			getWeeklyStats($lab_config, $test_type_id, $date_from, $date_to);
		}
		/* Single Test for Multiple Labs */
		else if ( $test_type_id != 0 && count($lab_config_id) > 1 ) {
			foreach( $lab_config_id as $key) {
				$lab_config = LabConfig::getById($key);
				$test_type_id = $testIds[$lab_config->id];
				$namesArray[] = $lab_config->name;
				getWeeklyStats($lab_config, $test_type_id, $date_from, $date_to);
			}
		}
	}
}

function getWeeklyStats( $lab_config, $test_type_id, $date_from, $date_to, $test_name = null ) {
			global $xAxisGraph;
			global $progressTrendsData;
			
			$stats = StatsLib::getDiscreteInfectionStatsWeekly($lab_config, $test_type_id, $date_from, $date_to);
			foreach($stats as $key => $value) {
				$formattedDate = bcmul($key,1000);
				if( $value[0] != 0) {
					$progressData[] = array($formattedDate,100-round(($value[1]/$value[0])*100,2));
				}	
				else {
					$progressData[] = array($formattedDate,0);
				}
			}
			$progressTrendsData[] = $progressData;
}

function getWeeklyStatsSingleLab( $lab_config, $lab_config_id, $date_from, $date_to, $test_name = null ) {
			global $xAxisGraph;
			global $progressTrendsData;
			global $labNamesArray;
			
			$test_type_list = get_discrete_value_test_types($lab_config);
			
			foreach($test_type_list as $test_type_id) {

				$stats = StatsLib::getDiscreteInfectionStatsWeekly($lab_config, $test_type_id, $date_from, $date_to);
				$namesArray[] = get_test_name_by_id($test_type_id, $lab_config_id);
				foreach( $stats as $key => $value ) {
					$xAxisGraph[] = date('Y,  n,  j',$key);
					if( $value[0] != 0 )
						$progressData[] = 100-round(($value[1]/$value[0])*100,2);
					else 
						$progressData[] = 0;
				}
				$progressTrendsData[] = $progressData;
				unset($progressData);
			}
	
}

$stat_list = array();
$stat_list = StatsLib::getDiscreteInfectionStatsAggregate($lab_config_id, $date_from, $date_to, $test_type_id);
if(count($stat_list) == 0) { ?>
	<div class='sidetip_nopos'>
		<?php echo LangUtil::$pageTerms['TIPS_NODISCRETE']; ?>
		</div>
		<?php
			include("includes/footer.php"); 
			return;
}
publishInfo( $lab_config_id, $test_type_id );
publishDates( $date_from, $date_to);
?>
<div id='stat_table'>
<?php
	if ( ( $test_type_id == 0 ) && ( $lab_config_id == 0 || count($lab_config_id) > 1 ) )
		$viewTrendsEnabled = true;
	else
		$viewTrendsEnabled = false;
	$page_elems->getInfectionStatsTableAggregate($stat_list, $date_from, $date_to, $test_type_id, $lab_config_id, $multipleIndividualLabs, $viewTrendsEnabled);
	processWeeklyTrends($lab_config_id, $test_type_id, $date_from, $date_to);
?>
</div>
<div id='trendsDiv_progress_spinner' style="display:none;">
		<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_FETCHING']); ?>
</div>
<?php

createGraph();
createTrends();

function createGraph() {

				global $stat_list;
				echo "<div id='stat_graph_bar'>";
				# To avoid cluttered graph, divide stat_list into chunks
				$chunk_size = 999;
				$stat_chunks = array_chunk($stat_list, $chunk_size, true);
				$i = 1;
				foreach($stat_chunks as $stat_chunk)
				{
					$div_id = "placeholder_".$i;
					$legend_id = "legend_".$i;
					$ylabel_id = "ylabel_".$i;
					$width_px = count($stat_chunk)*95;
					?>
					<table>
					<tbody>
					<tr valign='top'>
					<td>
						<span id="<?php echo $ylabel_id; ?>" class='flipv_up' style="width:30px;height:30px;"><?php echo LangUtil::$generalTerms['PREVALENCE_RATE']; ?> (%)</span>
					</td>
					<td>
						<div style='width:900px;height:340px;overflow:auto'>
							<div id="<?php echo $div_id; ?>" style="width:<?php echo $width_px; ?>px;height:300px;"></div>
						</div>
					</td>
					<td>
						<div id="<?php echo $legend_id; ?>" style="width:200px;height:300px;"></div>
					</td>
					</tr>
					</tbody>
					</table>
					
					<script id="source" language="javascript" type="text/javascript"> 
					$(function () {
						<?php
						$x_val = 0;
						$count = 1;
						foreach($stat_chunk as $key=>$value)
						{
							$test_type_id = $key;
							$count_all = $value[0];
							$count_negative = $value[1];
							$infection_rate = 0;
							if($count_all != 0)
								$infection_rate = round((($count_all-$count_negative)/$count_all)*100, 2);
							echo "var d$count = [];";
							echo "d$count.push([$x_val, $infection_rate]);";
							$count++;
							$x_val += 2;
						}
						?>
						$.plot($("#<?php echo $div_id; ?>"), [
							<?php
							$count = 1;
							$index_count = 0;
							$tick_array = "[";
							foreach($stat_chunk as $key=>$value)
							{
								$labName = $labNamesArray[$count-1];
								$tick_array .= "[$index_count+0.4, '$labName']";
								?>
								{
									data: d<?php echo $count; ?>,
									bars: { show: true, barWidth: 1 }//,
								}
								<?php
								$count++;
								$index_count += 2;				
								if($count < count($stat_chunk) + 1)
								{
									echo ",";
									$tick_array .= ",";
								}
							}
							$tick_array .= "]";
							?>
						], { xaxis: {ticks: <?php echo $tick_array; ?>}, legend: {container: "#<?php echo $legend_id; ?>"}  }
						);
						$('#<?php echo $ylabel_id; ?>').flipv_up();
					});
					</script>
					<?php
					# End of loop
					$i++;
				}
			echo "</div>";
}

function createTrends() { 
	global $namesArray;
	global $progressTrendsData;
?>

	<div id="trendsDiv" style="width: 800px; height: 400px; margin: 0 auto"></div>
	<script type="text/javascript">
		function viewTrends() {
			var progressData = new Array();
			var namesArray = <?php echo json_encode($namesArray); ?>;
			var progressTrendsDataTemp = <?php echo json_encode($progressTrendsData); ?>;
			
			var values, value1, value2;
			/* Convert the string timestamps to floatvalue timestamps */
			for(var j=0;j<progressTrendsDataTemp.length;j++) {
				var i = 0;
				if( progressTrendsDataTemp[j][i]) {
					progressData[j] = new Array();
					while ( progressTrendsDataTemp[j][i] ) {
						values = progressTrendsDataTemp[j][i];
						value1 = parseFloat(values[0]);
						value2 = values[1];
						progressData[j][i] = [value1, value2];
						i++;
					}
				}
			}
			
			$('#stat_graph').hide();
			$('#stat_graph_bar').hide();
			$('#viewGraphSpan').show();
			$('#hideGraphSpan').hide();
			$('#viewTrendSpan').hide();
			$('#hideTrendSpan').show();
			createChart(namesArray, progressData);
		}
	
		function createChart(namesArray, progressData) {
			var chart;
			var options = {
			chart: {
				 renderTo: 'trendsDiv',
				 type: 'spline'
			  },
			  title: {
				 text: 'Prevalence Rate'
			  },
			  xAxis: {
				 type: 'datetime',
				 dateTimeLabelFormats: { // don't display the dummy year
					month: '%e. %b',
					year: '%b'
				 }
			  },
			  yAxis: {
				 title: {
					text: 'Percentage (%)'
				 },
				 min: 0
			  },
			  tooltip: {
				 formatter: function() {
						   return '<b>'+ this.series.name +'</b><br/>'+
					   Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' %';
				 }
			  },
			  series: [{
				name : ' ', 
				tickInterval: 7 * 24 * 3600 * 1000, // one week
				pointStart: Date.UTC(2011, 0, 1),
				data: [ ]
			  }]
		   };
	   
			for(var i=0;i<namesArray.length;i++) {
				options.series.push({
					name: namesArray[i],
					data: progressData[i]
				});
			}
		   chart = new Highcharts.Chart(options);
		}
		
	</script>
<?php } 
include("includes/footer.php");
?>