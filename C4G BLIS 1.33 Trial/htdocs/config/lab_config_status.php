<?php
#
# Main page for showing lab configuration status
# Called via Ajax from lab_configs.php
#

include("redirect.php");
include("includes/page_elems.php");
LangUtil::setPageId("lab_configs");

$page_elems = new PageElems();
$lab_config_id = $_REQUEST['id'];
$page_elems->getLabConfigStatus($lab_config_id);
?>