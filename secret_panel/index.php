<?php
//OOO secret panel..
include("../db.php");
?>
<html>
	<head>
		<title>2bart: Semi anarchy art thing</title>
		<link type="text/css" rel="stylesheet" href="../2bart.css">
		</head>
		<body>
			<center>
				<?php require("header.php"); ?>
				<div class="art_background_container">
					<div class="art_container">
						<div class="art_header">
							<font class="art_header_text">Secret Panel</font>
						</div>
						<br>
							<form method="POST" action="index.php" class="container_center">
								<font size="2">
									<label>Username:</label>
								</font>
								<input type="text" name="username">
									<br>
										<font size="2">
											<label>Password:</label>
										</font>
										<input type="password" name="pass">
															<br>
										People with secret panel access could access this.
																<br>
																	<input type="submit" id="submit" value="get in" name="go_in">
																	</form>
																</div>
															</div>
														</center>
													</body>
												</html>
												