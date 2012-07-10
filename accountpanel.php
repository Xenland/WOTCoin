<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$wot_session = wot_verifySession();
wot_detect_session_ended(); //if there is no valid session, redirect user to the sessionended.php page (other wise do nothing)


//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h1>Account Panel</h1>
					Welcome to the account panel.<br/>
					<a href="/initiatetransaction">Initiate a transaction</a> (Used to initiate a transaction with another entity, use this before the transaction begins)
				</div>
				
				<div class="contentBox" style="margin-top:2em;">
					<h2 style="text-align:center;">Transaction(s) in Progress</h2>
					<span style="color:yellow;">yellow</span> awaiting your actions<br/>
					<span style="color:skyblue;">skyblue</span> both you and the other party are required actions<br/>
					<span style="color:purple;">purple</span>Awaiting on your party to do an action<br/><br/>
					<table style="width:99.99%;">
						<tr>
							<td style="text-align:center;font-weight:bold;">
								Timestamp
							</td>
							<td style="text-align:center;font-weight:bold;">
								Address
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
					<?php
						//Query for all transactions in progress
						$transaction_list_q = wot_doQuery("SELECT `id`, `timestamp_initiated`, `address_a_id`, `address_a`, `address_a_signature`, `address_a_status`, `address_b_id`, `address_b`, `address_b_status`, `address_b_signature` FROM `feedback_queue_index` WHERE `address_a` = ? OR `address_b` = ?", $wot_session["address"], $wot_session["address"]);
						while($transaction_list = $transaction_list_q->fetch()){
							//Precompute render
								//Timestamp
								$timestamp = $transaction_list["timestamp_initiated"];
							
								//Quickly organize who is me and who is not me
								$distinct_identities = wot_distinct_me_and_swim($transaction_list["address_a"], $transaction_list["address_a_status"], $transaction_list["address_b"], $transaction_list["address_b_status"]);
								
								//Who are we awaiting on? Me or SWIM?
								$status = wot_awaitingOnMeOrSwim($distinct_identities["me_status"], $distinct_identities["swim_status"]);
								
								if($status == 0){
									$t_style = "background-color:skyblue";
								}else if($status == 1){
									$t_style = "background-color:yellow";
								}else if($status == 2){
									$t_style = "background-color:purple;color:#fff;";
								}
					?>
						<tr style="text-align:center;<?php echo $t_style;?>">
							<td>
								<?php echo $transaction_list["timestamp_initiated"];?>
							</td>
							<td>
								<?php echo $distinct_identities["swim_address"];?>
							</td>
							<td>
								<a href="/txaction.php?id=<?php echo $transaction_list["id"];?>">View Transaction Details</a>
							</td>
						</tr>
					<?php
						}
					?>
					</table>
				</div>
				
				<div class="contentBox" style="margin-top:2em;">
					<h2 style="text-align:center;">Transaction(s) Completed | Feedback List</h2>
					
				</div>
			</div>
			
			<div id="ground">
				
			</div>
		</div>
	</div>
<?php
wot_footer();
?>