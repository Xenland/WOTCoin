<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$wot_session = wot_verifySession();
wot_detect_session_ended(); //if there is no valid session, redirect user to the sessionended.php page (other wise do nothing)

//Begin page logic
$wot_initiate_tx = wot_initiate_tx($_POST["btcaddress"], $_POST["message"], $_POST["signature"]);


//Include header of page
$wot_header_config["title"] = "Initiate a transaction";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h2>Initiate Transaction</h2>
					
				</div>
				
				<div class="contentBox" style="margin-top:2em;">
					<?php var_dump($wot_initiate_tx);?>
		
				</div>
			</div>
			
			<div id="ground">
				
			</div>
		</div>
	</div>
<?php
wot_footer();
?>