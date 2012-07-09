<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php wot_header_title(); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="/resources/css/general-style.css">
	</head>
	<body>
		<div id="reality">
			<div id="finiteSpace">
				<div id="sky">
					<h1 style="text-align:center;">Web of Trust | Bitcoin Authentication and Universal Feedback</h1>
					<?php
						if($wot_session["return_status"] == 1){
							echo "Welcome back!";
							echo '<a href="/accountpanel">Account Panel</a>';
						}
					?>
				</div>
			