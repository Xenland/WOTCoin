<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
*/
//Include required file and configurations
require_once("backend/linkage.php");

//Include header of page
$wot_header_config["title"] = "Home Safe Home.";
wot_header();
?>
			<div id="world">
				<div class="contentBox">
					<h2>WOTCoin session ended</h2>
					Your session has ended, For security purposes please re-authenticate. Some reason for this happening is you were logged in for more than an hour with out any activity or you are trying to access a page that requires a valid session and you are no longer logged in.
				</div>
			</div>
			
			<div id="ground">
				
			</div>
		</div>
	</div>
<?php
include($include_footer);
?>