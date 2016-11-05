<?php	

	require_once("../includes/init.php");
	require_once("../functions/admin.php");

	//Kill if users is not admin
	if(!$currentUser->isAdmin())
	{
		header("Location: /index.php");
		die();
	}

	//-----------------------------------------------------
	//Delete forum
	//-----------------------------------------------------
	if (isset($_GET['action']) && isset($_GET['id']))
	{
		if($_GET['action'] === "delete")
			deleteForum($_GET['id']);
	}

	//-----------------------------------------------------
	//New Forums
	//-----------------------------------------------------
	if(isset($_POST["newForum"])) 
	{
		newForum($_POST["forumName"], $_POST["description"], $_POST["category"], $_POST["ordering"], $_POST["guestAccess"]);
	}

	//-----------------------------------------------------
	//Get existing Forums
	//-----------------------------------------------------
	$stmt = $conn->prepare('SELECT id, name, category, ordering, guestAccess FROM forums');
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $category, $ordering, $guestAccess);

?>
<!DOCTYPE html>
<html>
	<head>
<?php include("../includes/standard_head.php"); ?>
		<title>Admin</title>
	</head>
	<body>
<?php include("../includes/navbar.php"); ?>
		<!-- Content start -->
		<div class="container">
<?php displayAlerts(); ?>
			<div class="row">
<?php include("../includes/admin_menu.php"); ?>
				<!-- Forums Start -->
				<div class="panel panel-default">
					<div class="panel-heading">
						Forums
						<button  type="button" class="btn btn-xs btn-success pull-right" data-toggle="collapse" data-target="#newForumWell">New Forum</button>
					</div>
					<div class="panel-body">
						<div id="newForumWell" class="well well-sm collapse">
							<form id="newForum" method="post" enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-4 col-sm-6">
										<div class="input-group">
											<span class="input-group-addon">Name: </span>
											<input type="text" name="forumName" class="form-control" placeholder="Forum name">
										</div>
									</div>
									<div class="col-md-3 col-sm-6">
										<div class="input-group">
											<span class="input-group-addon">Category:</span>
											<select class="form-control" name="category">
<?php
	$Categories =getAllCategoryIDs();
	foreach ($Categories as $category)
	{
		echo '<option value="'. $category .'">'. getCategoryName($category) .'</option>';
	} 
?>
											</select>
										</div>
									</div>
									<div class="col-md-2 col-sm-6">
										<div class="input-group">
											<span class="input-group-addon">Order:</span>
											<input type="text" name="ordering" class="form-control" size="1" placeholder="1">
										</div>
									</div>
									<div class="col-md-3 col-sm-6">
										<div class="input-group">
											<span class="input-group-addon">Allow Guest Access:</span>
											<span class="input-group-addon">
												<input type="checkbox" name="guestAccess">
											</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="input-group">
											<span class="input-group-addon">Description:</span>
											<textarea name="description" cols="30" rows="2" class="form-control"></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<div class="input-group">
											<button type="submit" name="newForum" class="btn btn-sm btn-success">Create Forum</button>
											<button type="button" class="btn btn-sm btn-default" data-toggle="collapse" data-target="#newForumWell">Cancel</button>
										</div>
									</div>
								</div>
							</form>
						</div>
						<!-- List existing Forums start-->
						<div style="overflow-x: auto;">
							<table class="table">
								<thead>
										<tr>
											<th>ID</th>
											<th>Name</th>
											<th>Category</th>
											<th>Sort Order</th>
											<th>Allow Guest Access</th>
											<th>#of Posts</th>
											<th></th>
											</tr>
								</thead>
								<tbody>
<?php
	while($stmt->fetch())
	{
?>
									<tr>
										<td><?php echo $id; ?></td>
										<td><?php echo $name; ?></td>
										<td><?php echo getCategoryName($category); ?></td>
										<td><?php echo $ordering; ?></td>
										<td><?php echo $guestAccess; ?></td>
										<td><?php echo numberOfPosts($id); ?></td>
										<td><a href="#", data-href="?action=delete&id=<?php echo $id; ?>" data-toggle="modal" data-target="#confirm-delete" class="btn btn-xs btn-danger pull-right">Delete</a><a href="editforum.php?id=<?php echo $id; ?>" class="btn btn-xs btn-success pull-right">Edit</a></td>
									</tr>
<?php
	}
	$stmt->free_result();
	$stmt->close();
?>
								</tbody>
							</table>
						</div>
						<!-- List existing Forums end-->
						</form>
					</div>
				</div>
				<!-- Forums End -->
				<!-- Modal confirmation start -->
				<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
							<div class="modal-content">
									<div class="modal-header">
											<h3>Warning!<h3>
									</div>
									<div class="modal-body">
											You're about to delete a forum.
											This will delete all posts and comments in that forum
											this can not be undone.
									</div>
									<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
											<a class="btn btn-danger btn-ok">Delete</a>
									</div>
							</div>
					</div>
				</div>
				<!-- Modal confirmation End -->
		</div>
		<!-- Content end -->
<?php include("../includes/standard_footer.php"); ?>
		<script src="/js/custom/admin-menu.js"></script> 
		<script>
			$('#confirm-delete').on('show.bs.modal', function(e) {
				$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
			});
		</script>
	</body>
</html>