<?php
/*

	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

$wot_session = wot_verifySession();

//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
					<?php include("backend/includes/general/leftside_menu.php");?>
					<div id="bodyContent">
						<?php
						include("backend/includes/general/transactionmenu.php");
						?>
						<p>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Welcome to WOTCoin, the first easy to use universal Bitcoin feedback system!
							Based on the tried and true web of trust format, this system provides a web
							based platform to build and display your trade history proudly, as it should be,
							because you earned it!
						</p><br/><br/>
						<p>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;As a buyer you can also rest assured knowing that your trading partner not
							only is who they say they are, but they also fulfil their registered agreements.
							Best of all, because the agreement process requires both parties to sign and
							then confirm the agreement, each transaction has the legal standing of a
							contractual agreement.
						</p><br/><br/>
						<p>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trade easy with WOTCoin! Register by entering your Bitcoin address above
							or search for current user profiles below.
						</p>
	
						<br/><br/>
						<div id="search">
							<h3 class="search">SEARCH USERS</h3>
							<input type="text" name="address" value="" id="searchInput" size="35" maxlength="34"/>
						</div>
					</div>
<?php
include($include_footer);
?>