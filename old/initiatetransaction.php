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


//Include header of page
$wot_header_config["title"] = "Initiate a transaction";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h2>Initiate Transaction</h2>
					
				</div>
				
				<div class="contentBox" style="margin-top:2em;">
					
					<form action="/initiatetransaction_step2.php" method="post">
						<b>Type in the Bitcoin Address Identity of the person you are about to commence a transaction with.</b>
						<br/>
						(This is <b>not</b> the address you are being asked to pay the other party with.)<br/>
						<input type="text" name="btcaddress" value="Bitcoin Address Identity " size="35"/>
						<br/>
						<br/>
						<b>Type in a message that describes the transaction you are about to commit.</b>
						<textarea cols="1" rows="1" style="width:99.99%;height:15em;" name="message">---BEGIN MESSAGE BLOCK---
The Bitcoin address owner of, <?php echo $wot_session["address"];?>
 agrees to provide &lt;Insert Bitcoin value, Services, Goods here&gt; to the following Bitcoin address owner of,  &lt;Insert address here&gt; as they will provide me with &lt;Insert Bitcoin value, Services, Goods here&gt;

This agreement initiated at 
<?php $time_initiated = time(); echo $time_initiated;?> (Epoch Timestamp)
<?php echo date("F/d/Y G:i:s", $time_initiated)." (Coordinated Universal Time)";?>

---END MESSAGE BLOCK---</textarea>
						<br/>
						<br/>
						<b>Sign the message you wrote above with the address of <?php echo $wot_session["address"];?> , declaring your approval</b>
						<input type="text" name="signature" value="" size="89" style="font-size:85%;"/>
						<br/>
						<br/>
						<input type="submit" value="Initiate Transaction"/>
					</form>
				</div>
			</div>
			
			<div id="ground">
				
			</div>
		</div>
	</div>
<?php
wot_footer();
?>