<?php
include("db.php");
$thing = $conn->query("SELECT * FROM arts ORDER BY id DESC"); 
$counter = 0;
?>
<html>
	<head>
		<title>2bart: Anarchy art thing</title>
	</head>
	<body>
		<center>
			<a href="/2bart"><img src="2bart.png"></a>
				<p>Welcome to 2bart</p>
				<a href="submit_art.php">Submit an art</a>
				<br>
					<br>
						<table border="1">
							<tr>
					<?php
					while($art = $thing->fetch_assoc()) {
					$counter++;
						?>
								<td align="center" width="106" height="136">
									<a href="art.php?id=<?php echo $art["id"]; ?>"><img src="<?php echo $art["image"]; ?>" width="100" height="100"></a>
										<br>
											<font size="2"><a href="art.php?id=<?php echo $art["id"]; ?>"><?php echo $art["name"]; ?></a>
												<br> by <?php echo $art["author"]; ?>
												</font>
											</td>
				<?php
				if ($counter == 3) {
					$counter = 0;
					echo "</tr>";
				}
					}
				?>
										</center>
									</body>
								</html>
