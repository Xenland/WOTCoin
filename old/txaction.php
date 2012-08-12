<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$wot_session = wot_verifySession();
wot_detect_session_ended(); //if there is no valid session, redirect user to the sessionended.php page (other wise do nothing)

//Declare variables
$act = $_GET["act"];
$tx_id = (int) $_GET["id"];

//Get tx information
$tx_information = wot_tx_queue_information($tx_id);

//Quickly organize who is me and who is not me
$distinct_identities = wot_distinct_me_and_swim($tx_information["db_data"]["address_a"], $tx_information["db_data"]["address_a_status"], $tx_information["db_data"]["address_b"], $tx_information["db_data"]["address_b_status"]);

if($act == "signmessage"){
	//Check if this user is required to sign message?
	if($distinct_identities["me_status"] == 0){
		//Check if the message is valid
		$validate_signed_message = verifyMessage($wot_session["address"], $_POST["signed_message"], $tx_information["db_data"]["message_to_sign"]);
		
		
		if($validate_signed_message == true){
			
			//The signature validates update it to transaction information, and change status
			wot_doQuery("UPDATE `feedback_queue_index` SET `address_b_signature` = ?, `address_b_status` = 1, `address_a_status` = 1 WHERE `id` = ? AND `address_b` = ? LIMIT 1", $_POST["signed_message"], $tx_id, $wot_session["address"]);
		
		
			//Reset information now that we have updated everything
			//Get tx information
			$tx_information = wot_tx_queue_information($tx_id);

			//Quickly organize who is me and who is not me
			$distinct_identities = wot_distinct_me_and_swim($tx_information["db_data"]["address_a"], $tx_information["db_data"]["address_a_status"], $tx_information["db_data"]["address_b"], $tx_information["db_data"]["address_b_status"]);
		}else{
			echo "That message is not valid";
		}
	}
}

if($act == "signfullfilledmessage"){
	//Check if this user is in the state of transaction to be commiting this (which is status = 1) and the other user is in status 1 or 2
	if($distinct_identities["me_status"] == 1){
		if($distinct_identities["swim_status"] == 1 || $distinct_identities["swim_status"] == 2){
			//Check if the signed message is valid
$message_to_check_against =  "---BEGIN MESSAGE---
I have fulfilled my duties for the message with the signature matching as the following: ".$tx_information["db_data"]["address_".$distinct_identities["a_or_b"]."_signature"]."
---END MESSAGE---"; //Don't alter this unless you are going to make it match the message in the <textarea></textarea> below

			$validate_signed_message = verifyMessage($wot_session["address"], $_POST["signed_message"], $message_to_check_against);
			
			if($validate_signed_message == true){
				//The signature validates update it to transaction information, and change status
				if($distinct_identities["a_or_b"] == "a"){
					wot_doQuery("UPDATE `feedback_queue_index` SET `address_a_signature_fulfilled` = ?, `address_a_status` = ? WHERE `id` = ? AND `address_a` = ? LIMIT 1", $_POST["signed_message"], 2, $tx_id, $wot_session["address"]);
				}else if($distinct_identities["a_or_b"] == "b"){
					wot_doQuery("UPDATE `feedback_queue_index` SET `address_b_signature_fulfilled` = ?, `address_b_status` = ? WHERE `id` = ? AND `address_b` = ? LIMIT 1", $_POST["signed_message"], 2, $tx_id, $wot_session["address"]);
				}
				echo "VALIDATED!";
				
			}else{
				echo "That message was not valid";
			}
			
		}else{
			echo "The transaction isn't ready to be fullfilled yet";
		}
	}else{
		echo "You are not at the correct step to be doing this action";
	}
}

if($act == "signswimfulfilledmessage"){
	//Check if this user is in the state of transaction to be commiting this ( which is status = 2) and the other user is in status 2
	if($distinct_identities["me_status"] == 2){
		if($distinct_identities["swim_status"] == 2){
$message_to_check_against = "---BEGIN MESSAGE---
I agree that the party of ".$distinct_identities["swim_address"]." has fulfilled their duties that are found in the message which signature can be validated with the following: ".$tx_information["db_data"]["address_".$distinct_identities["a_or_b"]."_signature"]."

---END MESSAGE---";

			$validate_signed_message = verifyMessage($wot_session["address"], $_POST["signed_message"], $message_to_check_against);
			
			if($validate_signed_message == true){
				//The signature validates update it to transaction information, and change status
				if($distinct_identities["a_or_b"] == "a"){
					wot_doQuery("UPDATE `feedback_queue_index` SET `address_a_confirms_swim_fulfillment_signature` = ?, `address_a_status` = ? WHERE `id` = ? AND `address_a` = ? LIMIT 1", $_POST["signed_message"], 3, $tx_id, $wot_session["address"]);
				}else if($distinct_identities["a_or_b"] == "b"){
					wot_doQuery("UPDATE `feedback_queue_index` SET `address_b_confirms_swim_fulfillment_signature` = ?, `address_b_status` = ? WHERE `id` = ? AND `address_b` = ? LIMIT 1", $_POST["signed_message"], 3, $tx_id, $wot_session["address"]);
				}
				
			}else{
				echo "that message was not valid";
			}
		}else{
			echo "The other party hasen't claimed they fullfilled their duties yet";
		}
	}else{
		echo "You haven't claimed you fullilled your duties yet";
	}
}

//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h1>Transaction Information (In- Progress)</h1>
					Original Message
					<div style="border-top:1px solid #000;margin-top:0.3em;margin-bottom:0.5em;"></div>
					<?php
						echo nl2br($tx_information["db_data"]["message_to_sign"]);
					?>
				</div>
				
				<?php
					//Figure out if there is any thing to display regarding this transaction that requires this user to act upon
					if($distinct_identities["me_status"] == 0){
				?>
					<div class="contentBox" style="margin-top:1em;">
						Awaiting on you to sign the message with the Bitcoin address of <?php echo $wot_session["address"];?><br/>
						<textarea cols="1" rows="1" style="width:99.99%;height:15em;"><?php echo $tx_information["db_data"]["message_to_sign"]; ?></textarea>
						<br/>
						<br/>
						<form action="/txaction.php?id=<?php echo $tx_id;?>&act=signmessage" method="post">
							<input type="text" name="signed_message" value="<?php if($_POST["signed_message"] != "Paste your signed message" && $_POST["signed_message"] != ""){ echo $_POST["signed_message"]; }else{ echo "Paste your signed message";}?>" size="88" style="font-size:90%;"/>
							<br/>
							<br/>
							<input type="submit" value="Sign &amp; Approve Message"/>
						</form>
					</div>
				<?php
					}
				?>
					
				
				<?php
					if($distinct_identities["me_status"] == 1){
						//Are we awaiting on the other party?
						if($distinct_identities["swim_status"] == 0){
				?>
						<div class="contentBox" style="margin-top:1em;">
							<b>Awaiting on the other party to accept the message</b><br/>
							<?php echo nl2br($tx_information["db_data"]["message_to_sign"]);?>
						</div>
				<?php
						}else if($distinct_identities["swim_status"] == 1 || $distinct_identities["swim_status"] == 2){
				?>
						<div class="contentBox" style="margin-top:1em;">
							<b>Both parties have accepted the message/agreement, They may now proceed with the physical transaction.</b>
							<br/>
							<br/>
							<?php echo nl2br($tx_information["db_data"]["message_to_sign"]);?>
						</div>
						<div class="contentBox" style="margin-top:1em;">
							<h3 style="text-align:center;">Have you fullilled your end of the bargin?</h3>
							<br/>
							<i>If you believe you have fullfilled your duties, sign and submit the message below</i><br/>
							<textarea rows="1" cols="1" style="width:99.99%;height:15em;">---BEGIN MESSAGE---
I have fulfilled my duties for the message with the signature matching as the following: <?php echo $tx_information["db_data"]["address_".$distinct_identities["a_or_b"]."_signature"];?>

---END MESSAGE---</textarea><br/><br/>
							<form action="/txaction.php?id=<?php echo $tx_id;?>&act=signfullfilledmessage" method="post">
								<input type="text" name="signed_message" value="Paste signature here" size="80"/>
								<br/><br/>
								<input type="submit" value="Sign Message"/>
							</form>
						</div>
				<?php
						
						}
					}
				?>
				
				
				
				<?php
					if($distinct_identities["me_status"] == 2){
						
						//Whats swim doing?
						if($distinct_identities["swim_status"] == 1){
				?>
						<div class="contentBox" style="margin-top:1em;">
							<b>Both parties have accepted and signed the agreement<br/>You have claimed you have fullfilled your end of the bargin.</b>
							<br/>
							<br/>
							<i>We are now awaiting on <?php echo $distinct_identities["swim_address"];?> to fullfill their duties</i>
						</div>
				<?php
						}else if($distinct_identities["swim_status"] == 2 || $distinct_identities["swim_status"] == 3){
				?>
						<div class="contentBox" style="margin-top:1em;">
							<b>Both parties have claimed to have fulfilled their duties, If you feel the other party has fulfilled their duties please sign and submit the singature of the message below</b>
							<textarea rows="1" cols="1" style="width:99.99%;height:15em;">---BEGIN MESSAGE---
I agree that the party of <?php echo $distinct_identities["swim_address"];?> has fulfilled their duties that are found in the message which signature can be validated with the following: <?php echo $tx_information["db_data"]["address_".$distinct_identities["a_or_b"]."_signature"];?>

---END MESSAGE---</textarea>
							<form action="txaction.php?id=<?php echo $tx_id;?>&act=signswimfulfilledmessage" method="post"/>
								<br/>
								<input type="text" name="signed_message" value="Paste signed messaged here" size="86" style="font-size:90%;"/>
								<br/>
								<br/>
								<input type="submit" value="Sign Message"/>
							</form>
						</div>
				<?php
						}
					}
				?>
				
				
				
				<?php
					if($distinct_identities["me_status"] == 3){
				?>
				
				<?php
					}
				?>
			</div>
			
			<div id="ground">
				
			</div>
		</div>
	</div>
<?php
wot_footer();
?>