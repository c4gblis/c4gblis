<?php
#
# Main page for creating a new lab configuration
#
include("redirect.php");
include("includes/header.php");
LangUtil::setPageId("lab_configs");

//$script_elems->enableJWizard(); 
$script_elems->enableDatePicker();
?>
<link rel='stylesheet' type='text/css' href='css/wizard_styles.css' />
<script type="text/javascript">

<?php $page_elems->getCompatibilityJsArray("st_map"); ?>

$(document).ready(function(){
	$('#tech_entries').show();
	$('.2').hide();
	$('.3').hide();
	$('.4').hide();
	$('.5').hide();
	
	$('.stype_entry').change(function() {
		//check_compatible();
	});
});

function check_compatible()
{
	return;
	$('.ttype_entry').attr("disabled", "disabled");
	$('.ttype_entry').removeAttr("checked");
	for(var i in st_map)
	{
		var stype_elem_id = "s_type_"+i;
		var stype_elem = $('#'+stype_elem_id);
		if(stype_elem == undefined || stype_elem == null)
			continue;
		if(stype_elem.attr("checked"))
		{
			var test_csv = st_map[i];
			if(test_csv == "" || test_csv == null || test_csv == undefined || typeof test_csv != 'string')
				continue;
			if(test_csv.contains(","))
			{
				var test_list = test_csv.split(",");
				for(var j in test_list)
				{
					var checkbox_elem_id = "t_type_"+j;
					var checkbox_elem = $('#'+checkbox_elem_id);
					checkbox_elem.removeAttr("disabled");
				}
			}
			else
			{
				var checkbox_elem_id = "t_type_"+test_csv;
				var checkbox_elem = $('#'+checkbox_elem_id);
				checkbox_elem.removeAttr("disabled");
			}
		}
	}
}
	
function loadnext(divout,divin){
	$("." + divout).hide();
	//$("." + divin).fadeIn("fast");
	$("." + divin).show();
}

function checkandadd()
{
	//Validate
	var name = $('#facility').attr("value");
	if(name == "")
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_FACILITY']; ?>");
		return;
	}
	var location = $('#location').attr("value");
	if(location == "")
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_LOCATION']; ?>");
		return;
	}
	var lab_admin = $('#lab_admin').attr("value");
	if(lab_admin == "")
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_MGR']; ?>");
		return;
	}
	var stype_entries = $('.stype_entry');
	var stype_selected = false;
	for(var i = 0; i < stype_entries.length; i++)
	{
		if(stype_entries[i].checked)
		{
			stype_selected = true;
			break;
		}
	}
	if(stype_selected == false)
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_STYPES']; ?>");
		return;
	}
	var ttype_entries = $('.ttype_entry');
	var ttype_selected = false;
	for(var i = 0; i < ttype_entries.length; i++)
	{
		if(ttype_entries[i].checked)
		{
			ttype_selected = true;
			break;
		}
	}
	if(ttype_selected == false)
	{
		alert("<?php echo LangUtil::$pageTerms['TIPS_MISSING_TTYPES']; ?>");
		return;
	}
	//All okay
	$('.5').hide();
	$('.6').show();
	$('#new_lab_form').submit();
}
</script>

<style type="text/css">
	#registration { width:950px; margin:20px; background:#F6F6F6; }
	#registration > div { padding:0 10px; }
</style>
<br>
<b><?php echo LangUtil::$pageTerms['NEW_LAB_CONFIGURATION']; ?>	</b>
 | <a href="javascript:history.go(-1);"><?php echo LangUtil::$generalTerms['CMD_CANCEL']; ?></a>
<br><br>
<form id='new_lab_form' name='new_lab_form' action='lab_config_add.php' method='post'>
<DIV id="wizardwrapper">

  <DIV class="1" style="display: block; ">
    <H3>1: <?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></H3>
    <DIV id="wizardcontent"> 
	<br>
		<table>
			<tr>
				<td width='130px'><?php echo LangUtil::$generalTerms['FACILITY']; ?><?php $page_elems->getAsterisk();?></td>
				<td><input type='text' name='name' id='facility' value='' class='uniform_width' /></td>
			</tr>
			<tr>
				<td><?php echo LangUtil::$generalTerms['LOCATION']; ?><?php $page_elems->getAsterisk();?></td>
				<td><input type='text' name='location' id='location' value='' class='uniform_width' /></td>
			</tr>
			<?php 
			//If user is superadmin
			if(true)
			{
			?>
			<tr>
				<td><?php echo LangUtil::$generalTerms['LAB_MGR']; ?> <?php $page_elems->getAsterisk();?></td>
				<td>
					<select name='lab_admin' id='lab_admin' class='uniform_width'>
					<?php 
					# Fetch list of existing lab admins 
					$page_elems->getAdminUserOptions();
					?>
					<!--<option value='0'>--New Admin Account--</option>-->			
					</select>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
	</DIV>
    <DIV class="buttons">
      <BUTTON type="button" class="previous" disabled="disabled"> <IMG src="js/jquery.jwizard/images/jwizard_arrow_left.png" alt=""> <?php echo LangUtil::$generalTerms['CMD_BACK']; ?> </BUTTON>
      <BUTTON type="button" class="next" onclick="loadnext(1,2);"> Next <IMG src="js/jquery.jwizard/images/jwizard_arrow_right.png" alt=""> </BUTTON>
    </DIV>
    <br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="current"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI><div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
    <DIV style="clear:both"></DIV>
  </DIV>
 
  <DIV id="wizardpanel" class="2" style="opacity: 1; display: none; ">
    <H3>2: <?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></H3>
    <DIV id="wizardcontent">
	<br>
	<?php $page_elems->getSpecimenTypeCheckboxes(); ?>
	</DIV>
    <DIV class="buttons">
      <BUTTON type="button" class="previous" onclick="loadnext(2,1);"> <IMG src="js/jquery.jwizard/images/jwizard_arrow_left.png" alt=""> <?php echo LangUtil::$generalTerms['CMD_BACK']; ?> </BUTTON>
      <BUTTON type="button" class="next" onclick="loadnext(2,3);"> Next <IMG src="js/jquery.jwizard/images/jwizard_arrow_right.png" alt=""> </BUTTON>
    </DIV>
    <br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="lastDone"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI class="current"><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI><div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
	<DIV style="clear:both"></DIV>
  </DIV>
  
  
  <DIV id="wizardpanel" class="3" style="opacity: 1; display: none; ">
    <H3>3: <?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></H3>
    <DIV id="wizardcontent">
	<br>
	<?php $page_elems->getTestTypeCheckboxes(); ?>
	</DIV>
    <DIV class="buttons">
      <BUTTON type="button" class="previous" onclick="loadnext(3,2);"> <IMG src="js/jquery.jwizard/images/jwizard_arrow_left.png" alt=""> <?php echo LangUtil::$generalTerms['CMD_BACK']; ?> </BUTTON>
      <BUTTON type="button" class="next" onclick="loadnext(3,4);"> Next <IMG src="js/jquery.jwizard/images/jwizard_arrow_right.png" alt=""> </BUTTON>
    </DIV>
    <br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="done"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI class="lastDone"><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI class="current"><div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
	<DIV style="clear:both"></DIV>
  </DIV>
 
  <DIV id="wizardpanel" class="4" style="opacity: 1; display: none; ">
    <H3>4: <?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></H3>
    <DIV id="wizardcontent">
	<br>
	<?php $page_elems->getOperatorForm(1); ?>
	</DIV>
    <DIV class="buttons">
      <BUTTON type="button" class="previous" onclick="loadnext(4,3);"> <IMG src="js/jquery.jwizard/images/jwizard_arrow_left.png" alt=""> <?php echo LangUtil::$generalTerms['CMD_BACK']; ?> </BUTTON>
      <BUTTON type="button" class="next" onclick="loadnext(4,5);"> Next <IMG src="js/jquery.jwizard/images/jwizard_arrow_right.png" alt=""> </BUTTON>
    </DIV>
    <br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="done"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI class="done"><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI class="lastDone"><div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI class="current"><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
	<DIV style="clear:both"></DIV>
  </DIV>
 
  <DIV id="wizardpanel" class="5" style="display: none; opacity: 1; ">
    <H3>5: <?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></H3>
    <DIV id="wizardcontent">
	<?php #Specimen and Patient ID &nbsp;&nbsp;&nbsp; ?>
		<select name='id_mode' id='id_mode' style='display:none;'>
			<option value='<?php echo LabConfig::$ID_MANUAL; ?>'>Manual</option>
			<option value='<?php echo LabConfig::$ID_AUTOINCR; ?>' selected>Auto-increment</option>
		</select>
		<br><br><br>
		<?php echo LangUtil::$pageTerms['TIPS_CUSTOM']; ?>
		<br><br><br><br>
		<?php echo LangUtil::$pageTerms['TIPS_CONFIRMNEWLAB']; ?>
		<span id='site_name'></span>
		&nbsp;&nbsp;&nbsp;&nbsp;<input id='add_button' type='button' value='<?php echo LangUtil::$generalTerms['CMD_ADD']; ?>' onclick='javascript:checkandadd();'></input>
	</DIV>
    <DIV class="buttons">
      <BUTTON type="button" class="previous" onclick="loadnext(5,4);"> <IMG src="js/jquery.jwizard/images/jwizard_arrow_left.png" alt=""> <?php echo LangUtil::$generalTerms['CMD_BACK']; ?> </BUTTON>
      <BUTTON type="button" class="next" disabled="disabled"> Next <IMG src="js/jquery.jwizard/images/jwizard_arrow_right.png" alt=""> </BUTTON>
    </DIV>
	<br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="done"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI class="done"><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI class="done"<div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI class="lastDone"><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg current"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
	<DIV style="clear:both"></DIV>
  </DIV>
  
   <DIV id="wizardpanel" class="6" style="display: none; opacity: 1; ">
    <H3><?php echo LangUtil::$generalTerms['CMD_SUBMITTING']; ?> ...</H3>
    <DIV id="wizardcontent">
	<center>
	<?php 
		$spinner_message = LangUtil::$pageTerms['TIPS_CREATINGLAB']."<br>";
		$page_elems->getProgressSpinnerBig($spinner_message);		
	?>
	</center>
	</DIV>
    <DIV class="buttons">
    </DIV>
	<br><br>
    <UL id="mainNav" class="fiveStep">
      <LI class="done"><div class='white_big'>Step 1:<br><?php echo LangUtil::$pageTerms['MENU_SITEINFO']; ?></div></LI>
      <LI class="done"><div class='white_big'>Step 2:<br><?php echo LangUtil::$generalTerms['SPECIMEN_TYPES']; ?></div></LI>
      <LI class="done"<div class='white_big'>Step 3:<br><?php echo LangUtil::$generalTerms['TEST_TYPES']; ?></div></LI>
      <LI class="lastDone"><div class='white_big'>Step 4:<br><?php echo LangUtil::$generalTerms['TECHNICIANS']; ?></div></LI>
      <LI class="mainNavNoBg current"><div class='white_big'>Step 5:<br><?php echo LangUtil::$pageTerms['MENU_FIELDS']; ?></div></LI>
    </UL>
	<DIV style="clear:both"></DIV>
  </DIV>
  
</DIV>
<!--Random Data Section-->
	<div id='random' style='display:none'>
	<b>Random Data</b> [<a rel='facebox' href='#randomdata_help'>?</a>]
		<br><br>
		<?php
		$today = date("Y-m-d");
		$today_array = explode("-", $today);
		?>
		<table class='smaller_font' cellspacing='5px'>
		<tr>
		<td>Number of Patients</td>
		<td><input type='text' name='num_patients' value="150" /></td>
		</tr>
		<tr>
		<td>Number of Specimens</td>
		<td><input type='text' name='num_specimens' value="1000" /></td>
		</tr>
		<tr>
		<td>Specimen Collection Dates:</td>
		<td></td>
		</tr>
		<tr valign='top'>
		<td>From</td>
		<td>
		<?php
		$name_list = array("yyyy_from", "mm_from", "dd_from");
		$id_list = $name_list;
		//$value_list = array($today_array[0], "01", "01");
		$value_list = array("2009", "01", "01");
		$page_elems->getDatePicker($name_list, $id_list, $value_list); 
		?>
		</td>
		</tr>
		<tr valign='top'>
		<td>To</td>
		<td>
		<?php
		$name_list = array("yyyy_to", "mm_to", "dd_to");
		$id_list = $name_list;
		$value_list = $today_array;
		$page_elems->getDatePicker($name_list, $id_list, $value_list); 
		?>
		</td>
		</tr>
		</table>
		<div id='randomdata_help' style='display:none;'>
		<table class='smaller_font'>
		<tr>
		<td>
		<b>Random Data</b>
		<br><br>
		Currently, due to lack of existing data in the system, random data records are added for testing purposes.
		The number of patient and specimen records along with specimen collection date range can be specified as parameters for the random data generator.
		<br><br>
		This feature is only for evaluation phases and will not be part of the system when deployed.
		</td>
		</tr>
		</table>
		</div>
	</div>
	<!--End of Random Data Section-->
	
</form>
<br>
<?php include("includes/footer.php"); ?>
<!--
		<table cellpadding='5px'>
			<tbody>
				<tr>
					<td>
						'Specimen ID' field
					</td>
					<td>
						<select class='uniform_width'>
							<option value='1'>Manual</option>
							<option value='2'>Auto-increment</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						'Patient ID' field
					</td>
					<td>
						<select class='uniform_width'>
							<option value='1'>Manual</option>
							<option value='2'>Auto-increment</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Use 'Additional ID' field for Specimens?
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td>
						<select class='uniform_width'>
							<option value='1'>Yes</option>
							<option value='0'>No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Use 'Additional ID' field for Patients?
					</td>
					<td>
						<select class='uniform_width'>
							<option value='1'>Yes</option>
							<option value='0'>No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Test Results Verification
					</td>
					<td>
						<select class='uniform_width'>
							<option value='1'>Not required</option>
							<option value='2'>Optional</option>
							<option value='3'>Mandatory</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		-->