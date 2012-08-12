<?php
if($wot_session["address_id"] > 0 && $wot_session["address"] !=''){
?>
<div id="transactionmenu">
	<a href="/transactions?show=pending"><div style="width:15em;height:3em;" onMouseDown="">
		<span class="transactionmenuNumber">103</span>
	</div></a>
	
	<a href="/transactions?show=completed"><div style="width:17em;height:1.6em;">
		<span class="transactionmenuNumber" style="width:4em;">10</span>
	</div></a>
	
	<a href="/transactions?show=feedback"><div style="width:17em;height:1.6em;">
		<span style="float:right;color:green;">(+ 20)</span> <span style="float:right;color:red;margin-right:0.5em;">(- 10)</span>
	</div></a>
</div>
<?php
}
?>