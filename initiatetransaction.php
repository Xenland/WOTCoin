<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");
date_default_timezone_set("UTC");

$wot_session = wot_verifySession();
wot_detect_session_ended(); //if there is no valid session, redirect user to the sessionended.php page (other wise do nothing)

$step = $_GET["step"];
if($step == '' || $step == 0){
	$step = 1;
}

if($step==2){
	//Address valid
	$bitcoin_connection = OpenBitcoinClient_noconnection();
	$address_valid = $bitcoin_connection["connection_tunnel"]->checkAddress($_POST["btcaddress"]);
	
	if($address_valid != 1){
		$step = 1;
		$address_not_valid = 1;
	}
}

if($step == 4){
	$wot_initiate_tx = wot_initiate_tx($_POST["btcaddress"], trim($_POST["message"]), trim($_POST["signature"]));
	if($wot_initiate_tx["return_status"] == 100){
		$step = 1;
	}else if($wot_initiate_tx["return_status"] == 102){
		$step = 3;
	}else if($wot_initiate_tx["return_status"] = 101){
		$step = 3;
	}else if($wot_initiate_tx["return_status"] == 1){
		$step = 4;
	}else if($wot_initiate_tx ["return_status"] != 1){
		$step = 3;
	}
}

print_r(verifyMessage($wot_session["address"], htmlspecialchars_decode($_POST["message"]), $_POST["signature"]));

//Include header of page
$wot_header_config["title"] = "Initiate a transaction";
wot_header();
?>
					<?php 
					$wot_leftside_menu["selected"] = 2;
					include("backend/includes/general/leftside_menu.php");?>
					<div id="bodyContent">
						<?php
						include("backend/includes/general/transactionmenu.php");
						?>
							<?php
							if($step == 1){
							?>
							<div style="margin-top:7em;">
								<b>Type in the Bitcoin Address Identity of the person you are about to commence a transaction with.</b>
								<br/>
								(This is <b>not</b> the address you are being asked to pay the other party with.)<br/>
							</div>
							
							<form action="/initiatetransaction.php?step=2" method="post">
							<div class="contbox1Container" style="width:99.99%;margin-top:2em;">
								<div class="contbox1Content">
									<input type="text" name="btcaddress" class="contbox1SingleLineInput" value="<?php if($_POST["btcaddress"] == ''){ echo 'Type Bitcoin Address Identity Here';}else{ echo htmlspecialchars($_POST["btcaddress"]);}?>" size="35" maxlength="34" style="width:99.99%;" onFocus="if(this.value=='Type Bitcoin Address Identity Here'){this.value=''}" onBlur="if(this.value==''){this.value='Type Bitcoin Address Identity Here';}"/>
									<?php if($address_not_valid == 1){ echo "<div style=\"color:#F27F0B;\">That Bitcoin does not appear to be valid</div>";}?>
								</div>
							</div>
							<input type="submit" value="Continue to step 2 of initiation" class="submit" style="margin-top:1em;">
							</form>
							<?php
							}else if($step == 2){
							?>
							<form action="/initiatetransaction.php?step=3" method="post">
							<input type="hidden" name="btcaddress" value="<?php echo $_POST["btcaddress"];?>"/>
							
							<div style="margin-top:7.5em;">
							<b>Type in a message that describes the transaction you are about to commit.</b>
							</div>
							<div class="contbox1Container" style="width:99.99%;float:left;">
								<div class="contbox1Content">
									<?php
									if($_POST["message"] == ''){
									?>
									<textarea cols="1" rows="1" style="width:99.99%;height:20em;" name="message">---[BEGIN MESSAGE BLOCK]---
The Bitcoin address owner of, <?php echo $wot_session["address"];?> agrees to provide &lt;Insert Bitcoin value, Services, Goods here&gt; to the following Bitcoin address owner of, <?php echo $_POST["btcaddress"];?> as they will provide me with &lt;Insert Bitcoin value, Services, Goods here&gt;

This agreement initiated at 
<?php $time_initiated = time(); echo $time_initiated;?> (Epoch Time stamp)
<?php echo date("F/d/Y G:i:s", $time_initiated)." (Coordinated Universal Time)";?>

---[END MESSAGE BLOCK]---</textarea><?php }else{ ?><textarea cols="1" rows="1" style="width:99.99%;height:20em;" name="message"><?php echo htmlspecialchars($_POST["message"]);?></textarea><?php }?>
								</div>
								<input type="submit" value="Continue to the next step" class="submit" style="margin-top:1em;"/>
							</div>
							
							</form>
							<?php
							}else if($step == 3){
							?>
							<?php print_r($wot_initiate_tx);?>
							<form action="/initiatetransaction.php?step=4" method="post">
							<input type="hidden" name="btcaddress" value="<?php echo $_POST["btcaddress"];?>"/>
							<input type="hidden" name="message" value="<?php $post_message = $_POST["message"]; $post_message = str_replace("\\r\\n",'',$post_message); echo $post_message;?>"/>
							<div style="margin-top:7.5em;">
								<b>Copy the message below, paste it into your Bitcoin Client "sign messages" section and pasted the signed message in the box below</b>
							</div>
							<div class="contbox1Container" style="width:99.99%;">
								<div class="contbox1Content"><?php echo nl2br(htmlspecialchars($_POST["message"]));?></div>
							</div>
							<div class="contbox1Container" style="width:99.99%;margin-top:1em;">
								<div class="contbox1Content">
									<textarea name="signature" cols="1" rows="1" style="width:99.99%;height:3em;min-height:3em;font-size:120%;" onFocus="if(this.value=='Paste the signed message from your Bitcoin client into this text box'){this.value=''}" onBlur="if(this.value==''){this.value='Paste the signed message from your Bitcoin client into this text box';}">Paste the signed message from your Bitcoin client into this text box</textarea>
								</div>
								<input type="submit" value="Sign Initiation" class="submit" style="margin-top:1em;"/>
							</div>
							</form>
							
							
							<?php
							}else if($step == 4){
							?>
							<h3>You are now awaiting on the other party to accept and sign the agreement to initiate a transaction</h3>
							<?php
							}
							?>
						</div>
					</div>
<?php
wot_footer();
?>