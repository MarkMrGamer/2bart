<?php
include("db.php");

//since im terrible at pagination, here is some code from another repo
$total_arts = $conn->query('SELECT COUNT(*) FROM arts')->fetch_row()[0]; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$art_numbers = 9;

$thing = $conn->prepare("SELECT * FROM arts ORDER BY id DESC LIMIT ?,?"); 
$calc_arts = ($page - 1) * $art_numbers;
$thing->bind_param("ii", $calc_arts, $art_numbers);
$thing->execute();
$things = $thing->get_result();
$counter = 0;
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
							<font class="art_header_text">Arts / Images</font>
						</div>
						<table class="art_panel" border="0">
							<tr>
								<?php
					while($art = $things->fetch_assoc()) {
					$counter++;
						?>
								<td align="center" width="115" height="136">
									<a href="art.php?id=<?php echo $art["id"]; ?>"><img class="container_art_picture" src="<?php echo $art["image"]; ?>" width="100" height="100"></a>
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
								<div class="container_pagination">
									<?php if (ceil($total_arts / $art_numbers) > 0): ?>
									<?php if ($page > 1): ?>
									<a class="container_pagination_link" href="index.php?page=<?php echo $page-1; ?>">Back</a> 
									<?php endif; ?>

									<?php if ($page < ceil($total_arts / $art_numbers)): ?>
									<a class="container_pagination_link" href="index.php?page=<?php echo $page+1; ?>">Next</a>
									<?php endif; ?>
									<?php endif; ?>
								</font>
							</div>
						</div>
					</center>
				</body>
			</html>
			