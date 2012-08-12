<div id="leftsidemenu">
	<?php
	$wot_leftside_home_css = "leftside-entry";
	$wot_leftside_login_css = "leftside-entry";
	$wot_leftside_search_css = "leftside-entry";
	$wot_leftside_faq_css = "leftside-entry";
	$wot_leftside_contact_css = "leftside-entry";
	
	if($wot_leftside_menu["selected"] == 0 || $wot_leftside_menu["selected"] == 1){
		$wot_leftside_home_css = "leftside-entry-selected";
	}
	
	if($wot_leftside_menu["selected"] == 2){
		$wot_leftside_login_css = "leftside-entry-selected";
	}
	
	if($wot_leftside_menu["selected"] == 3){
		$wot_leftside_search_css = "leftside-entry-selected";
	}
	
	if($wot_leftside_menu["selected"] == 4){
		$wot_leftside_faq_css = "leftside-entry-selected";
	}
	
	if($wot_leftside_menu["selected"] == 5){
		$wot_leftside_contact_css = "leftside-entry-selected";
	}
	
	?>
	<div class="<?php echo $wot_leftside_home_css;?>">
		<a href="/" class="leftside-entry-link">Home</a>
	</div>
	
	<?php
	if($wot_session["return_status"] != 1 && $wot_session["address_id"] <= 0){;
	?>
	<div class="leftside-entry">
		<a href="/login" class="leftside-entry-link">Login</a>
	</div>
	<?php
	}else{
	?>
	<div class="<?php echo $wot_leftside_login_css;?>">
		<a href="/initiatetransaction" class="leftside-entry-link">Initiate Transaction</a>
	</div>
	<?php
	}?>

	<div class="leftside-entry">
		<a href="/search" class="leftside-entry-link">Search users</a>
	</div>
	<div class="leftside-entry">
		<a href="#" class="leftside-entry-link">F.A.Q.</a>
	</div>
	<div class="leftside-entry">
		<a href="#" class="leftside-entry-link">Contact</a>
	</div>	
	<?php
		if($wot_leftside_menu["selected"] == -1){
	?>
	<div class="leftside-entry-selected">
		<a href="javascript:void(0);" class="leftside-entry-link"><?php echo $wot_leftside_menu["selected_title"];?></a>
	</div>
	<?php
		}
	?>
	
	
	<?php
	if($wot_session["return_status"] == 1 && $wot_session["address_id"] > 0){
	?>
	<div class="leftside-entry">
		<a href="/logout" class="leftside-entry-link">Logout</a>
	</div>
	<?php
	}
	?>
</div>