<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");


//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>

	<div id="reality">
		<div id="finiteSpace">
			<div id="sky">
				<h1 style="text-align:center;">Web of Trust | Bitcoin Authentication and Universal Feedback</h1>
			</div>
			
			<div id="world">
				<div class="contentBox">
					<h2>Register with WOTCoin.com</h2>
					<form action="/registerAddress.php" method="post">
						<input type="text" name="btcaddress" value="Type a Bitcoin address that you own here" size="35" style="margin:1em;" onFocus="if(this.value=='Type a Bitcoin address that you own here'){ this.value=''}" onBlur="if(this.value==''){ this.value='Type a Bitcoin address that you own here';}"/>
						<input type="submit" value="Next Step &gt;&gt;"/>
					</form>
				</div>
				
				<div class="contentBox" style="margin-top:5em;">
					<h2>Search a registered Bitcoin Address</h2>
					<form action="/searchAddress.php" method="post">
					<input type="text" name="btcaddress" value="Type in a Bitcoin address to search for here" size="35" style="margin:1em;" onFocus="if(this.value=='Type in a Bitcoin address to search for here'){ this.value=''}" onBlur="if(this.value==''){ this.value='Type in a Bitcoin address to search for here';}"/>
					<input type="submit" value="Next Step &gt;&gt;"/>
				</div>
			</div>
			
			<div id="ground">
			
			</div>
		</div>
	</div>
<?php
include($include_footer);
?>