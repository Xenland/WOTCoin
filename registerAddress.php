<?php
//Include required file and configurations
require_once("backend/linkage.php");

//Connect to Bitcoin
$bitcoin = new Bitcoin();

//Convert variables
$address = $_POST["btcaddress"];

//Attempt to register address
$attempt_registration_step1 = register_address_step1($address);

//Include header of page
$wot_header_config["title"] = "Register with WOTCoin";
wot_header();
?>
	<div id="reality">
		<div id="finiteSpace">
			<div id="sky">
				<h1 style="text-align:center;">Web of Trust | Bitcoin Authentication and Universal Feedback</h1>
			</div>
			
			<div id="world">
				<div class="contentBox">
					<?php
						if($attempt_registration_step1["return_status"] != 1){
							echo '<div><a href="/">&lt;- Go back</a></div>';
							echo '<div style="color:red;">'.$attempt_registration_step1["return_status_message"]."</div>";
						}
					?>
					<?php
						if($attempt_registration_step1["return_status"] == 1){
					?>
					<h2>One more thing to register</h2>
					<div>Please copy the random letters and numbers found in the box below, and paste it into the "Sign Message" box in your Bitcoin client exactly.</div>
					<textarea cols="1" rows="1" style="width:69em;height:8em;"><?php echo $attempt_registration_step1["authentication_message"];?></textarea>
					<h2>Enter your signed message below</h2>
					<form action="/registerAddress_step2.php" method="post">
						<input type="hidden" name="btcaddress" value="<?php echo $address;?>"/>
						<input type="text" name="signedmessage" value="" size="90" style="font-size:90%;"/>
						<input type="submit" value="Register Address"/>
					</form>
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