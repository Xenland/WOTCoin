<!DOCTYPE HTML>
<html>
	<head>
		<title>Web of Trust &amp; Universal Feedback</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>   

		<!--Reset CSS Code-->
		<link rel="StyleSheet" href="/resources/css/reset-style.css" type="text/css" media="screen">
		<!-- Styling Code -->
		<link rel="StyleSheet" href="/resources/css/main-style.css" type="text/css" media="screen">
		
		<script type="text/javascript">
			$(document).ready(function(){
				var documentHeight = $(document).height();
				var documentWidth = $(document).innerWidth();
					documentWidth -= 20;
				$("#leftsidemenu").css("height", documentHeight+"px");
				$("#contentBG").css("height", documentHeight+"px");
				$("#contentBG").css("width", documentWidth+"px");
			});
		</script>
	</head>
	<body>
		<div id="contentBG">
			<div id="content">
				<div id="header">
					<div id="logo"></div>
					<div id="addressContainer">
						<div id="addressInputContainer">
							<div id="addressInputContent">
								<?php
								if($wot_session["return_status"] != 1){
								?>
								Log in or Register Address: <input type="text" name="address" value="" size="30" maxlength="34" id="loginAddressInput"/>
								<?php
								}else{
								?>
								Search Address <input type="text" name="address" value="" size="30" maxlength="34" id="loginAddressInput"/>
								<?php
								}
								?>
							</div>
						</div>
						<div id="headerTitleContainer">
							WEB OF TRUST &amp; UNIVERSAL FEEDBACK
						</div>
					</div>
				</div>
				<div id="body">