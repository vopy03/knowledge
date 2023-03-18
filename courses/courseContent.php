<?php require_once "../dbconnect.php"; ?>
<?php if ($_REQUEST['value'] == 0) { // Головна 

	$result = $pdo->prepare("SELECT user_position FROM members WHERE user_id = :user_id AND course_id = :course_id");
	$params = [
	"course_id" => $_REQUEST['courseId'],
	"user_id" => $_SESSION['id'],
	]; 
	$result->execute($params);
	$user_position = $result->fetchAll();
	$user_position = $user_position[0][0];


	$result = $pdo->prepare("SELECT * FROM courses WHERE id = :course_id ");
	$params = [
	"course_id" => $_REQUEST['courseId'],
	]; 
	$result->execute($params);
	$course = $result->fetchAll();

	?>

<div class="courseAjaxMainPage">
	<div class="courseAjaxMainPageContent" >
		<div class="courseAMPContentStream" >
			<!-- <h4>Повідомлення в курсі</h4> -->
			<?php if ($course[0]['allow_post'] == 1 || $user_position == 1) { ?>
			<form method="POST" action="../courses/<?php echo $_REQUEST['courseId'] ?>">
				<div class="courseAMPContentStreamPost">
				<div class="streamPostHeader" style="display:inline-flex;" >

					<img src="<?php echo $_SESSION['avatar']; ?>">
					<div >
						<b><?php echo $_SESSION['full_name']; ?></b>
					</div>
					
				</div>
				<div class="input-group mt-2">
				  <input class="form-control" autocomplete="off" type="text" name="post_text" placeholder="Ваш текст" required>
				  <button  class="input-group-text btn btn-dark" name="publish_post" ><span style="color:white" class="material-icons" title="Відправити">send</span></button>

				  <button type="button" class="input-group-text btn btn-dark" data-bs-toggle="modal" data-bs-target="#selectTest" name="publish_post" ><span style="color:white" class="material-icons" title="Додати тест">post_add</span></button>
				  


				  <div class="modal fade " id="selectTest" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons-outlined">post_add</span>Додати тест</h4>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					      </div>
					      <div class="modal-body">


					      	

					      	<!-- Список тестів -->
					      	<?php 

					      	$result = $pdo->prepare("SELECT * FROM tests WHERE creator_id = :user_id");
							$params = [
							"user_id" => $_SESSION['id'],
							]; 
							$result->execute($params);
							$userTests = $result->fetchAll();

							if ($userTests) {

							foreach ($userTests as $key => $test) {
							
							

					      	?>
					      	<div class="streamPostTestPart" onclick="addTest(<?php echo $test['id']; ?>, '<?php echo $test['name']; ?>' )" data-bs-dismiss="modal" >
								<span class="material-icons-outlined" >assignment</span>
								<div>
								<span><b><?php echo $test['name'] ?></b></span>
								<p style="font-size:10px"><span>Дата створення: <?php echo $test['date_of_creating'] ?></span> </p>
								<p style="font-size:10px" ><span>Питань: <?php echo $test['num_of_questions']; ?></span></p>
								</div>
							</div>
							<?php } } else {?>
							<!-- Немає тестів -->

					      	<div style="height: 300px;">
					      		<div class="course404"><span class="material-icons">&#xe5cd</span><br><p>У вас немає тестів</p></div>
					      	</div>
					      <?php } ?>


					      </div>
					      <div class="modal-footer">
					      <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>

					      </div>
					    </div>
					  </div>
					</div>
					



				</div>
				<div id="postTestPart"></div>
			</div>
				
			</form>
			<script type="text/javascript">
				var postTestPart = document.getElementById('postTestPart');
			function addTest(testID, testName) {
				
				postTestPart.innerHTML = '';
				var p = document.createElement('p');
				var input = document.createElement('input');
				p.innerHTML = 	'<div class="streamPostTestPart" " >'+
								'<span class="material-icons-outlined testIcon" >assignment</span>'+
								'<span>'+ testName +'</span>'+
								'<span class="material-icons removeTestPartButton" onclick="removeTest()" >close</span>'+
								'</div>';
				input.hidden = true;
				input.style.display = 'none';
				input.type = 'number';
				input.name = 'testId';
				input.value = testID;
				postTestPart.appendChild(p);
				postTestPart.appendChild(input);
			}
			function removeTest() {
				postTestPart.innerHTML = '';
			}

			</script>

			<?php }

	      	$result = $pdo->prepare("SELECT course_posts.text, course_posts.p_id, course_posts.test_post_id, course_posts.post_date, users.avatar, users.first_name, users.last_name, users.id FROM course_posts, users WHERE course_id = :course_id AND course_posts.user_id = users.id ORDER BY course_posts.post_date DESC");
			$params = [
			"course_id" => $_POST['courseId'],
			]; 
			$result->execute($params);
			$coursePosts = $result->fetchAll();



			$result = $pdo->prepare("SELECT * FROM tests");
			$params = [
			"course_id" => $_POST['courseId'],
			]; 
			$result->execute($params);
			$tests = $result->fetchAll();

			foreach ($tests as $key => $test) {
				$allTests[$test['id']] = $test;
			}
			

			foreach ($coursePosts as $key => $post) {
		
			 ?>
			<div class="courseAMPContentStreamPost">
				<div class="streamPostHeader" style="display:inline-flex;" >

					<img src="<?php echo $post['avatar']; ?>">
					<div ><b><?php echo $post['first_name'].' '.$post['last_name']; ?></b><br>
					<span style="font-size:12px"><?php echo $post['post_date']; ?></span></div>


					
				</div>
				<?php if($post['id'] == $_SESSION['id'] || $user_position  == 1 ) { ?>
				<form method="POST" action="../courses/<?php echo $_REQUEST['courseId'];?>" >
				<input type="number" name="p_id" value="<?php echo $post['p_id'] ?>" hidden>
				<button class="material-icons-outlined deletePostButton" name="delete_post" >delete</button>
				</form>
				<?php } ?>
				<div class="streamPostContent" >
					<p><?php echo $post['text']; ?></p>
				</div>
				<?php if ($post['test_post_id'] != NULL) { ?>
				<a href="../tests/s/<?php echo $post['test_post_id'] ?>">
					<div class="streamPostTestPart" >
						<span class="material-icons-outlined testIcon" >assignment</span>
						<span><?php if(!isset($allTests[$post['test_post_id']]['name'])) { echo 'Тест було видалено';} else { echo $allTests[$post['test_post_id']]['name']; } ?></span>
					</div>
				</a>
				<?php } ?>
			</div>
		<?php } ?>


		</div>
	</div>
</div>

<?php } ?>
<?php if ($_REQUEST['value'] == 1) { // Тести 
	//echo $_REQUEST['value'];

	$result = $pdo->prepare("SELECT course_posts.test_post_id, course_posts.post_date, tests.id, tests.name, tests.num_of_questions FROM course_posts, tests WHERE course_id = :course_id AND course_posts.is_test = 1 AND course_posts.test_post_id = tests.id ORDER BY course_posts.post_date DESC");
	$params = [
	"course_id" => $_REQUEST['courseId'],
	]; 
	$result->execute($params);
	$courseTests = $result->fetchAll();


	foreach ($courseTests as $key => $test) {
	

  	?>
  	<a href="../tests/s/<?php echo $test['test_post_id'] ?>">
  	<div class="streamPostTestPart">
		<span class="material-icons-outlined testIcon" >assignment</span>
		<div>
		<span><b><?php echo $test['name'] ?></b></span>
		<p style="font-size:10px"><span>Скинуто в потік: <?php echo $test['post_date'] ?></span> </p>
		<p style="font-size:10px" ><span>Питань: <?php echo $test['num_of_questions']; ?></span></p>
		<div class="testPartVRButton">
	      	<a href="../tests/vr/<?php echo $test['id']; ?>"><div class="testsMyTestsAction rounded" title="Переглянути результати">
	      		<span class="material-icons-outlined" >preview</span>
	      	</div></a>
      	</div>
		</div>
	</div>
	</a>
	<?php } ?>


	
	
<?php } ?>
<?php if ($_REQUEST['value'] == 2) { // Студенти 

	//echo $_REQUEST['value'];

	$result = $pdo->prepare("SELECT login, first_name, last_name, avatar, user_position FROM users, members WHERE user_id = users.id AND members.course_id = :course_id");
	$params = [
	"course_id" => $_REQUEST['courseId'],
	]; 
	$result->execute($params);
	$course = $result->fetchAll();
	?>
	<h4><span class="material-icons-outlined">manage_accounts</span>Адміністратор</h4>
	<hr style="margin: 0; margin-bottom: 10px;">
	<?php
	for ($i=0; $i < count($course) ; $i++) { ?>

		<?php if($course[$i]['user_position'] == 1) { ?>
		<div class="courseUserListItem">
			<img src="<?php echo $course[$i]['avatar']; ?>">
			<p><?php echo $course[$i]['first_name'] . ' ' . $course[$i]['last_name']; ?></p>
		</div>
	<?php } } ?>
	<br>
	<h4><span class="material-icons-outlined">groups</span>Учасники <b style="font-size:13px; margin: 0 5px">(<?php echo count($course)-1; ?>)</b></h4>

	<hr style="margin: 0; margin-bottom: 10px;">
	<?php
	for ($i=0; $i < count($course) ; $i++) { ?>
		<?php if($course[$i]['user_position'] == 0) { ?>
		<div class="courseUserListItem">
			<img src="<?php echo $course[$i]['avatar']; ?>">
			<p><?php echo $course[$i]['first_name'] . ' ' . $course[$i]['last_name'];?></p>
		</div>
	<?php } } ?>


					


	
<?php } ?>