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

//Who are we awaiting on? Me or SWIM?
$status = wot_awaitingOnMeOrSwim($distinct_identities["db_data"]["me_status"], $distinct_identities["db_data"]["swim_status"]);

if($act == "signmessage"){
	//Check if this user is required to sign message?
	if($distinct_identities["me_status"] == 0){
		//Check if the message is valid
		$Bitcoin_connection = OpenBitcoinClient();
		$validate_signed_message = $Bitcoin_connection["connection_tunnel"]->query("verifymessage", $wot_session["address"], $_POST["signed_message"], $tx_information["db_data"]["message"]);
		if($validate_signed_message == true){
			//The signature validates update it to transaction information, and change status
			wot_doQuery("UPDATE `feedback_queue_index` SET `address_b_signature` = ?, `address_b_status` = 1 WHERE `id` = ? AND `address_b` = ? LIMIT 1", $_POST["signed_message"], $tx_id, $wot_session["address"]);
		
		
			//Reset information now that we have updated everything
			//Get tx information
			$tx_information = wot_tx_queue_information($tx_id);

			//Quickly organize who is me and who is not me
			$distinct_identities = wot_distinct_me_and_swim($tx_information["db_data"]["address_a"], $tx_information["db_data"]["address_a_status"], $tx_information["db_data"]["address_b"], $tx_information["db_data"]["address_b_status"]);

			//Who are we awaiting on? Me or SWIM?
			$status = wot_awaitingOnMeOrSwim($distinct_identities["db_data"]["me_status"], $distinct_identities["db_data"]["swim_status"]);

		}else{
		
		}
	}
}

//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h1>Transaction Information (In - Progress)</h1>
				</div>
				
				<?php
					//Figure out if there is any thing to display regarding this transaction that requires this user to act upon
					if($distinct_identities["me_status"] == 0){
					?>
					<div class="contentBox" style="margin-top:1em;">
						Awaiting on you to sign the message with the Bitcoin address of <?php echo $wot_session["address"];?><br/>
						<input value="<?php echo $tx_information["db_data"]["message_to_sign"]; ?>"/>
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
					
					if($distinct_identities["me_status"] == 1){
						//Are we awaiting on the other party?
						if($distinct_identities["swim_status"] == 0){
				?>
						<div class="contentBox" style="margin-top:1em;">
							Awaiting on the other party to accept the message<br/>
							<?php echo $tx_information["db_data"]["message_to_sign"];?>
						</div>
				<?php
						}else if($distinct_identities["swim_status"] == 1){
				?>
						<div class="contentBox" style="margin-top:1em;">
							<b>Both parties have accepted the message/agreement, They may now proceed with the physical transaction.</b>
							<br/>
							<br/>
							<?php echo nl2br($tx_information["db_data"]["message_to_sign"]);?>
						</div>
				<?php
						}
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