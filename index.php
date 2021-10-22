<?php
include("db.php");

//since im terrible at pagination, here is some code from another repo
$total_arts = $conn->query('SELECT COUNT(*) FROM arts')->fetch_row()[0]; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$art_numbers = 3;

$thing = $conn->prepare("SELECT * FROM arts ORDER BY id DESC LIMIT ?,?"); 
$calc_arts = ($page - 1) * $art_numbers;
$thing->bind_param("ii", $calc_arts, $art_numbers);
$thing->execute();
$things = $thing->get_result();
$counter = 0;

$updates = $conn->query("SELECT * FROM blog ORDER BY id DESC");
?>
<html>
	<head>
		<title>2bart: Semi anarchy art thing</title>
		<link type="text/css" rel="stylesheet" href="2bart.css">
		</head>
		<body>
			<center>
				<?php require("header.php"); ?>
				<div class="art_background_container">
					<div class="art_container">
						<div class="art_header">
							<font class="art_header_text">What's 2bart?</font>
						</div>
						<div style="margin-left: 5px; margin-top: 2px; margin-bottom: 2px;">
							<font size="2">
                            2bart is a semi-anarchy art site where you could post art, images and any type anonyomusly 
						    (or not anonyomusly) on the website. </font>
							<br>
								<br>
									<font size="2">
							Any anonyomus/non-anonyomus users can show 
							their art or their image editing skills to other anonyomus/non-anonyomus users on the site 
							where people could judge whatever they like it or not.
						</font>
								</div>
							</div>
							<div class="art_container">
								<div class="art_header">
									<table width="510">
										<tr>
											<td align="left">
												<font class="recent_header_text">Recent Submissions</font>
											</td>
											<td align="right">
												<a class="container_pagination_link" href="arts.php">More Submissions</a>
											</td>
										</tr>
									</table>
								</font>
							</div>
							<table border="0" width="510" height="90">
								<tr>
							<?php
					while($art = $things->fetch_assoc()) {
					$counter++;
						?>
									<td align="center" width="130" height="80">
										<a href="art.php?id=<?php echo $art["id"]; ?>"><img class="container_art_picture" src="<?php echo $art["image"]; ?>" width="50" height="50"/></a>
										<br>
											<font class="container_art_title">
												<a class="container_art_link" href="art.php?id=<?php echo $art["id"]; ?>"><?php echo $art["name"]; ?>
											</a>
										</font>
										<br>
											<font class="container_art_author">by <?php echo $art["author"]; ?>
											</font>
										</font>
									</td>
									<?php
				if ($counter == 3) {
					$counter = 0;
					echo "</tr>";
				}
					}
				?>
								</table>
							</div>

							<?php
					while($blog = $updates->fetch_assoc()) {
						?>
							<div class="art_container">
								<div class="art_header">
									<font class="art_header_text"><?php echo $blog["name"]; ?> <font size="-1">by <?php echo $blog["author"]; ?> (<?php echo $blog["date"]; ?>)</font></font>
								</div>
								<div style="margin-left: 5px; margin-top: 2px; margin-bottom: 2px;">
									<font size="2">
                                    <?php echo $blog["description"]; ?>
						            </font>
										</div>
									</div>
					<?php
					}
					?>
								</div>
							</center>
						</body>
					</html>
			