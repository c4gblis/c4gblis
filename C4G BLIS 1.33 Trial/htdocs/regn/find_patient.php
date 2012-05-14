<?php
#
# Main page for starting patient lookup
# 1st step of specimen registration
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("find_patient");

$script_elems->enableDatePicker();
$script_elems->enableJQueryForm();

$lab_config = get_lab_config_by_id($_SESSION['lab_config_id']);
?>
<script type='text/javascript'>
$(document).ready(function() {
	$('#psearch_progress_spinner').hide();
	$('#add_anyway_link').attr("href", "new_patient.php");
	$('#pq').focus();
	$('#p_attrib').change(function() {
		$('#pq').focus();
	});
});

function fetch_patients()
{
	$('#psearch_progress_spinner').show();
	var patient_id = $('#pq').attr("value").trim();
	var search_attrib = $('#p_attrib').attr("value");
	var check_url = "ajax/patient_check_name.php?n="+patient_id;
	$.ajax({ url: check_url, success: function(response){
			if(response == "false" && search_attrib == 1)
			{
				$('#psearch_progress_spinner').hide();
				window.location="new_patient.php?n="+patient_id+"&jmp=1";
			}
			else
			{
				continue_fetch_patients();
			}
		}
	});
}

function continue_fetch_patients()
{
	var patient_id = $('#pq').attr("value").trim();
	var search_attrib = $('#p_attrib').attr("value");
	$('#psearch_progress_spinner').show();
	if(patient_id == "")
	{
		$('#psearch_progress_spinner').hide();
		$('#add_anyway_div').show();
		return;
	}
	var url = 'ajax/search_p.php';
	$("#patients_found").load(url, 
		{q: patient_id, a: search_attrib}, 
		function(response)
		{
			$('#psearch_progress_spinner').hide();
			if(search_attrib == 1)
			{
				$('#add_anyway_link').html(" If not this name '<b>"+patient_id+"</b>' <?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?>&raquo;");
				$('#add_anyway_link').attr("href", "new_patient.php?n="+patient_id);
			}
			else
			{
				$('#add_anyway_link').html("If not this name. <?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?> &raquo;");
				$('#add_anyway_link').attr("href", "new_patient.php");
			}
			$('#add_anyway_div').show();
		}
	);
}
</script>

<p style="text-align: right;"><a rel='facebox' href='#Registration'>Page Help</a></p>
<span class='page_title'><?php echo LangUtil::getTitle(); ?></span>
<!--| <a href='new_patient.php' title='Click to add a new patient in the system'>Add New Patient &raquo;</a>-->
<br><br>
<form>
	<select name='p_attrib' id='p_attrib' style='font-family:Tahoma;'>
		<?php $page_elems->getPatientSearchAttribSelect(); ?>
	</select>
	&nbsp;&nbsp;
	<input type='text' name='pq' id='pq' style='font-family:Tahoma;' />
	&nbsp;&nbsp;
	<input type='button' value='<?php echo LangUtil::$generalTerms['CMD_SEARCH']; ?>' id='psearch_button' onclick="javascript:fetch_patients();" />
	&nbsp;&nbsp;&nbsp;
	<span id='psearch_progress_spinner'>
	<?php $page_elems->getProgressSpinner(LangUtil::$generalTerms['CMD_SEARCHING']); ?>
	</span>
</form>
<br>
<div id='Registration' class='right_pane' style='display:none;margin-left:10px;'>
	<ul>
		<?php
		if(LangUtil::$pageTerms['TIPS_REGISTRATION_1']!="-") {
			echo "<li>";
			echo LangUtil::$pageTerms['TIPS_REGISTRATION_1'];
			echo "</li>";
		}	
		if(LangUtil::$pageTerms['TIPS_REGISTRATION_2']!="-") {
			echo "<li>"; 
			echo LangUtil::$pageTerms['TIPS_REGISTRATION_2'];
			echo "</li>";
		}
		if(LangUtil::$pageTerms['TIPS_PATIENT_LOOKUP']!="-")	{
			echo "<li>"; 
			echo LangUtil::$pageTerms['TIPS_PATIENT_LOOKUP'];
			echo "</li>"; 
		}
		?>
	</ul>
</div>
<div id='patients_found' style='position:relative;left:10px;'>
</div>
<br>
<div id='add_anyway_div' style='display:none'>
<a id='add_anyway_link' href='new_patient.php'><?php echo LangUtil::$pageTerms['ADD_NEW_PATIENT']; ?> &raquo;</a>
</div>
<br>
<br>
<?php $script_elems->bindEnterToClick('#pq', '#psearch_button'); ?>
<?php include("includes/footer.php"); ?>