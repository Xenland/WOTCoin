<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$wot_session = wot_verifySession();
wot_detect_session_ended(); //if there is no valid session, redirect user to the sessionended.php page (other wise do nothing if there is a valid session)

if($_GET["show"] == ''){
	$show = "pending";
}else if($_GET["show"] == "pending"){
	$show = "pending";
}else if($_GET["show"] == "completed"){
	$show="completed";
}else if($_GEt["show"] == "feedback"){
	$show="feedback";
}else{
	//All else fails
	$show="pending";
}

//Include header of page
$wot_header_config["title"] = "Transactions List";
wot_header();
?>
					<?php 
					$wot_leftside_menu["selected"] = -1;
					$wot_leftside_menu["selected_title"] = "Transactions";
					include("backend/includes/general/leftside_menu.php");?>
					<div id="bodyContent">
						<?php
						include("backend/includes/general/transactionmenu.php");
						?>
						<div style="float:right;width:30em;height:6.5em;padding-bottom:2em;">
							<?php
							if($show == "pending"){
								echo "&middot;Hover your mouse over any transaction to see the details about it appear on the right hand side. <br/><br/>&middot;Click on a pending transaction in the list(left-hand side) and you will be taken to transaction page where you can view the current status of the transaction.";
							}
							?>
						</div>
						
						<?php
						if($show == "pending"){
						?>
						<div class="contbox1Container" style="float:left;width:26em;">
							<h3 style="text-align:center;padding-bottom:0.5em;font-size:120%;">Transactions awaiting your involvment</h3>
							<div class="contbox1Content">
							<?php
							//Query for all transactions in progress
							$transaction_list_q = wot_doQuery("SELECT `id`, `timestamp_initiated`, `address_a_id`, `address_a`, `address_a_signature`, `address_a_status`, `address_b_id`, `address_b`, `address_b_status`, `address_b_signature` FROM `feedback_queue_index` WHERE `address_a` = ? OR `address_b` = ?", $wot_session["address"], $wot_session["address"]);
							$i=0;
							while($transaction_list = $transaction_list_q->fetch()){
								//Precompute render
										//Timestamp
										$timestamp = $transaction_list["timestamp_initiated"];
									
										//Quickly organize who is me and who is not me
										$distinct_identities = wot_distinct_me_and_swim($transaction_list["address_a"], $transaction_list["address_a_status"], $transaction_list["address_b"], $transaction_list["address_b_status"]);
										
										//Who are we awaiting on? Me or SWIM?
										$status = wot_awaitingOnMeOrSwim($distinct_identities["me_status"], $distinct_identities["swim_status"]);
									
									if($status != 2){
										$i++;
										if($i == 1){
											$t_style = "background-color:transparent;";
											$a_style = "color:skyblue;";
										}else{
											$t_style = "background-color:skyblue;";
											$a_style = "color:#FFF;";
										}
										
										if($i==1){
											$i = -1;
										}
							?>
								<div style="<?php echo $t_style;?>padding:0.5em;">
									<a href="/view_transaction.php?id=<?php echo $transaction_list["id"];?>" class="transactionpendinglink" style="<?php echo $a_style;?>" onMouseOver="showTx('<?php echo $transaction_list["id"];?>');"><?php echo $distinct_identities["swim_address"];?></a>
								</div>
						
							<?php
									}
								}
							?>
							</div>
						</div>
						

						<script type="text/javascript">
							var lastTxId = 0;
							function showTx(div_id){
								//Hide last tx div
								$("#awaitingActions"+lastTxId).css("display", "none");
								
								
								//Show this tx div
								$("#awaitingActions"+div_id).css("display", "");
								
								lastTxId = div_id;
								
							}	
						</script>
						<div class="contbox1Container" style="float:right;width:19em;">
							<h3 style="text-align:center;padding-bottom:0.5em;font-size:120%;">Transaction Details</h3>
							<div class="contbox1Content">
							<?php
							//Query for all transactions in progress
							$transaction_list_q = wot_doQuery("SELECT `id`, `message_to_sign`, `timestamp_initiated`, `address_a_id`, `address_a`, `address_a_signature`, `address_a_status`, `address_b_id`, `address_b`, `address_b_status`, `address_b_signature` FROM `feedback_queue_index` WHERE (`address_a` = ? && `address_b_status` >= `address_a_status`) OR (`address_b` = ? && `address_a_status` >= `address_b_status`)", $wot_session["address"], $wot_session["address"]);
							$i=0;
							while($transaction_list = $transaction_list_q->fetch()){
								//Precompute render
								//Who are we awaiting on? Me or SWIM?
								$status = wot_awaitingOnMeOrSwim($distinct_identities["me_status"], $distinct_identities["swim_status"]);
									
									//Timestamp
									$timestamp = $transaction_list["timestamp_initiated"];
								
									//Quickly organize who is me and who is not me
									$distinct_identities = wot_distinct_me_and_swim($transaction_list["address_a"], $transaction_list["address_a_status"], $transaction_list["address_b"], $transaction_list["address_b_status"]);
									
									
									if($status == 0){
										$status_output = "You and the other party are required actions";
									}else if($status == 1){
										$status_output = "You are required actions";
									}else if($status == 2){
										$status_output = "The other party is required actions";
									}
									
							?>
								<div id="awaitingActions<?php echo $transaction_list["id"];?>" style="display:none;width:16em;word-wrap:break-word;"><?php echo $status_output;?><br/><br/><?php echo nl2br($transaction_list["message_to_sign"]);?></div>
							<?php
							}
							?>
							</div>
						</div>
						
						<div class="contbox1Container" style="float:left;width:26em;margin-top:3em;">
							<h3 style="text-align:center;padding-bottom:0.5em;font-size:120%;">Transactions not awaiting your involvement</h3>
							<div class="contbox1Content">
							</div>
						</div>
						<?php
						}
						?>

					</div>
					<!--
			<div id="world">
				<div class="contentBox">
					<h1>Account Panel</h1>
					Welcome to the account panel.<br/>
					<a href="/initiatetransaction">Initiate a transaction</a> (Used to initiate a transaction with another entity, use this before the transaction begins)
				</div>
				
				<div class="contentBox" style="margin-top:2em;">
					<h2 style="text-align:center;">Transaction(s) in Progress</h2>
					<span style="color:yellow;background-color:#000;">awaiting your actions</span><br/>
					<span style="color:skyblue;background-color:#000;">both you and the other party are required actions</span><br/>
					<span style="color:orange;background-color:#000;">Awaiting on the other party to do an action</span><br/><br/>
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
									$t_style = "background-color:orange;color:#fff;";
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
	</div>-->
<?php
wot_footer();
?>