<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$login_step2 = wot_login_step2($_POST["btcaddress"], $_POST["signed_message"]);


//Include header of page
$wot_header_config["title"] = "Login Step 2";
wot_header();
?>

			<div id="world">
				<div class="contentBox">
					<?php
						/* Display nessecary elements for step 1 */
						if($login_step2["return_status"] == 1){
						
					?>
						Welcome! <?php echo $_POST["btcaddress"];?>
					<?php
						}else if($login_step2["return_status"] != 1){
					?>
						<div style="color:red;"><?php echo $login_step2["return_status_message"];?></div>
					<?php
						}
					?>
				</div>
			</div>
			
			<div id="ground">
			
			</div>
		</div>
	</div>
<?php
wot_footer();
?>