<?php 
include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/header.php');

echo $pecio->get('complete_menu');
?>


<div id="content">
<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/post-data.php'); ?>
</div>
<hr>
<?php

echo $pecio->get('sidebar_texts');
echo $pecio->get('sidebar_links');

?>

<?php 
include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/footer.php');
?>