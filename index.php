<?php require_once "./php/html_templates.php";

$tasks = [];

basic_setup(false);

if (isset($uid)) {
	$tasks      = get_tasks();
	$h_body_end = function () { ?>
		<div class="modal fade modal-xl" tabindex="-1" id="delete-confirm-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="task-details-title"></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<pre id="task-details-description" style="white-space: pre-line"></pre>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<script>
		const modal            = new bootstrap.Modal(document.getElementById("delete-confirm-modal"));
		const modalTitle       = document.getElementById("task-details-title");
		const modalDescription = document.getElementById("task-details-description");

		function displayDetails(i, description, status) {
			modalTitle.innerText       = `Task #${i}: ${status}`;
			modalDescription.innerText = description;
			modal.show();
		}
		</script>
	<?php };
}

show_html_start_block(PageIndex::Home);
show_messages(); ?>
	<h1 class="display-1 text-center"><?= TITLE_HTML ?></h1>
<?php if (isset($uid)) { ?>
	<form action="add_task.php" method="post">
		<div class="input-group">
			<textarea class="form-control" name="description" required rows="1"></textarea>
			<button type="submit" class="btn btn-primary" name="submit" value="y">Add Task</button>
		</div>
	</form>
	<?php
	if ($tasks !== false) {
		if (count($tasks) == 0) {
			echo "<p>No tasks found</p>";
		} else { ?>
			<table class="table table-striped table-hover mt-3">
				<tr>
					<th>#</th>
					<th>Task</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<?php
				$i = 0;
				foreach ($tasks as $task) {
					$id          = $task['id'];
					$description = $task['description'];
					$status      = $task['status'];
					?>
					<tr class="<?= $status === TASK_DONE ? "table-success" : "table-warning" ?>">
						<td><?= ++$i ?></td>
						<td><?php
							if (strlen($description) > 100) {
								echo substr($description, 0, 100), "...";
							} else {
								echo $description;
							}
							?>
						</td>
						<td><?= $status ?></td>
						<td colspan="2">
							<div class="btn-group">
								<button type="button" class="btn btn-info" onclick="displayDetails(<?= "$i,`$description`,'$status'" ?>)">View more</button>
								<?php
								if ($status != TASK_DONE) { ?>
									<a href="set_task_done.php?id=<?= $id ?>" class="btn btn-success">Done</span></a>
								<?php } ?>
								<a href="delete_task.php?id=<?= $id ?>" class="btn btn-danger">Delete</span></a>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		<?php }
	}
	?>
<?php } else { ?>
	<h4>To access the <?= TITLE_HTML ?> <a href="<?= Page::$pages[PageIndex::SignIn]->path ?>">sign in</a> or
		<a href="<?= Page::$pages[PageIndex::SignIn]->path ?>">register</a> first.</h4>
<?php } ?>
<?php
show_html_end_block();
