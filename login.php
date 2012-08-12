<?php
/*

	Dev Author: Shane Betz
*/
//Include required file and configurations
require_once("backend/linkage.php");

//Invoke session check
$wot_session = wot_verifySession();

//Define variables
$_POST["step"] == $_POST["step"]; //Convert string into integer (recasting)
if($_POST["step"] == '' || $_POST["step"] == 1 || $_POST["step"] == 0){
	$step = 1;
}else{
	$step = $_POST["step"];
}

if($step == 2){
	//An address is inputted make sure the address is valid before allow step 2
	$bitcoin = OpenBitcoinClient_noconnection();
	$address_valid = $bitcoin["connection_tunnel"]->checkAddress($_POST["address"]);
	
	if($address_valid != 1){
		//This address isn't valid redirect them back to step 1
		$step = 1;
	}else{
		//This address is valid, generate a message for this user to sign
		$step = 2;
		
		$step2_message = wot_login_step1($_POST["address"]);
		
		if($step2_message["return_status"] != 1){
			$step = 1;
		}
	}
}

if($step == 3){
	$step3_message = wot_login_step2($_POST["address"], $_POST["signedmessage"]);

	if($step3_message["return_status"] != 1){
		$step = 2;
		
		//Generate a different message to sign
		$step2_message = wot_login_step1($_POST["address"]);
	}else if($step3_message["return_status"] == 1){
		$step = 3;
	}
}

//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
					<?php include("backend/includes/general/leftside_menu.php");?>
					<div id="bodyContent">
						<?php
						if($step == 1){
						?>
						Type in a Bitcoin address below, that you own to identify your self.
						<br/>
						<form action="login.php" method="post">
							<input type="hidden" name="step" value="2"/>
							<input type="text" name="address" value="Bitcoin Address Here" class="loginaddress" size="33" maxlength="34" onFocus="if(this.value=='Bitcoin Address Here'){ this.value=''}" onBlur="if(this.value==''){this.value='Bitcoin Address Here'){this.value='';}"/>
							<br/><br/>
							<input type="submit" value="Take me to step 2" style="margin-left:0.1em;" class="login"/>
						</form>
						<?php
						}else if($step == 2){
						?>
						
						To login/register copy the message in the text box below and paste it into the "Sign Message" box found in your Bitcoin Client.
						<br/><br/>
						After signing the message, copy the signed message and paste it in the "paste your signed message here" box on the wotcoin website.
						<br/><br/>
						<div class="contbox1Container" style="width:30em;float:left;">
							<div class="contbox1Content">
								<textarea col="1" rows="1" style="width:99.99%;height:16em;" class="loginmessage"><?php echo $step2_message["authentication_message"];?></textarea>
							</div>
						</div>
						
						<div class="contbox1Container" style="width:15em;float:right;">
							<form action="login.php" method="post">
							<input type="hidden" name="address" value="<?php echo htmlspecialchars($_POST["address"]);?>"/>
							<input type="hidden" name="step" value="3"/>
							<div class="contbox1Content">
								<textarea col="1" rows="1" style="width:99.99%;height:10em;" class="signmessage" name="signedmessage" onFocus="if(this.value=='Paste your signed message here'){ this.value='';}" onBlur="if(this.value==''){ this.value='Paste your signed message here';}">Paste your signed message here</textarea>
							</div>
							<div class="loginbutton">
								<input type="submit" value="Authenticate My Identity" class="login"/>
							</div>
							</form>
						</div>
						
						<?php
						}else if($step == 3){
						?>
						<div style="text-align:center;">
							<h3 style="font-size:150%;">Welcome Back</h3>
							<br/><br/>
							<a href="/transactions">Click here for Transactions List</a><br/>
							<br/>
							<a href="/accountsettings">Click here for Account Settings</a>
						</div>
						<?php
						}
						?>
					</div>
<?php
include($include_footer);
?>