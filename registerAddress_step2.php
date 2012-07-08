<?php
//Include required file and configurations
require_once("backend/linkage.php");


$attempt_registration_step2 = register_address_step2($_POST["btcaddress"], $_POST["signedmessage"]);
//Include header of page
include($include_header);
?>
	<div id="reality">
		<div id="finiteSpace">
			<div id="sky">
				<h1 style="text-align:center;">Web of Trust | Bitcoin Authentication and Universal Feedback</h1>
			</div>
			
			<div id="world">
				<div class="contentBox">
					<?php
						if($attempt_registration_step2["return_status"] != 1){
							echo '<div><a href="/registerAddress.php">&lt;- Goback</a></div>';
							echo '<div style="color:red;">'.$attempt_registration_step2["return_status_message"]."</div>";
						}
					?>
					<?php
						if($attempt_registration_step2["return_status"] == 1){
					?>
						Welcome! <?php echo $_POST["btcaddress"]; ?>
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
include($include_footer);
?>