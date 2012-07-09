<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$login_step1 = wot_login_step1($_POST["btcaddress"]);


//Include header of page
$wot_header_config["title"] = "Login Step 2";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<?php
						/* Display nessecary elements for step 1 */
							if($login_step1["return_status"] != 1){
							?>
								<div style="color:red;"><?php echo $login_step1["return_status_message"];?></div>
								<form action="/login.php" method="post">
									<input type="text" name="btcaddress" value="<?php echo $_POST["btcaddress"];?>" size="35"/>
									<input type="submit" value="Next Step &gt;&gt;"/>
								</form>
							<?php
							}else if($login_step1["return_status"] == 1){
								//The address is valid, show the message required to sign
								
							?>
								<textarea cols="1" rows="1" style="width:99.99%;height:8em;"><?php echo $login_step1["authentication_message"];?></textarea>
								<br/>
								<br/>
								<form action="/login_step2.php" method="post">
									<input type="hidden" name="btcaddress" value="<?php echo $_POST["btcaddress"];?>"/>
									<input type="text" name="signed_message" value="" size="69"/>
									<br/>
									<input type="submit" value="Next Step &gt;&gt;"/>
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
wot_footer();
?>