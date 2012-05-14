<?php
#
# Main page for lab configuration added confirmation
#
include("redirect.php");
include("includes/header.php");
?>
<br>
<b>Lab Configuration added</b>
 | <a href='lab_configs.php'>&laquo; Back to Configurations</a>
<br><br>
<?php
$page_elems->getLabConfigInfo($_REQUEST['id']);
include("includes/footer.php");
?>