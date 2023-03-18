<?php
require_once "../dbconnect.php";
include '../data/libs/userCheck.php';
	userCheck();

	if (isset($_GET['delete_courseid'])) { // Перевіряє, чи натиснута кнопка "Видалити курс"

		$deleteID = $_GET['delete_courseid'];

		$sql = "DELETE FROM members WHERE course_id = :course_id";

		$statement = $pdo->prepare($sql);
		$statement->bindParam(':course_id', $deleteID, PDO::PARAM_INT);
		$statement->execute();


		$sql = "DELETE FROM courses WHERE id = :course_id";

		$statement = $pdo->prepare($sql);
		$statement->bindParam(':course_id', $deleteID, PDO::PARAM_INT);
		$statement->execute();

		
		header('Location: ../courses');
	}
	if (isset($_GET['leave_courseid'])) { // Перевіряє, чи натиснута кнопка "залишити курс"

		$courseID = $_GET['leave_courseid'];

		$sql = "DELETE FROM members WHERE course_id = :course_id AND user_id = :user_id ";

		$statement = $pdo->prepare($sql);
		$statement->bindParam(':course_id', $courseID, PDO::PARAM_INT);
		$statement->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
		$statement->execute();
		
		header('Location: ../courses');
	}




	if (isset($_POST['create_course'])) { // Перевіряє, чи натиснута кнопка "Створити курс"


		$permitted_chars = '0123456789ABCDEFGHIJKLMNOPRSTUVWXYZ';
		$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
		$category_id = $_POST['category_id'];
		$is_public = (isset($_POST['is_public'])) ? 1 : 0 ;
		$invite_code = substr(str_shuffle($permitted_chars), 0, 7);

    		$sql = "INSERT INTO `courses` (`title`, `invite_code`, `category_id`) VALUES (:title, :invite_code, :category_id)";

    		$result = $pdo->prepare($sql);
			$params = [
			"title" => $title,
			"invite_code" => $invite_code,
			"category_id" => $category_id,
			]; 
			$result->execute($params);

			$result = $pdo->prepare("SELECT id FROM courses WHERE title = :title ");
			$params = [
			"title" => $title,
			]; 
			$result->execute($params); 
			$id = $result->fetchAll();
			$course_id = $id[0][0];
			$sql = "INSERT INTO `members` (`course_id`, `user_id`, `user_position`) VALUES (:course_id, :user_id, :user_position)";

    		$result = $pdo->prepare($sql);
			$params = [
			"course_id" => $course_id,
			"user_id" => $_SESSION['id'],
			"user_position" => 1,
		];
			$result->execute($params);
			//header('Location: ../courses');
	}

	if (isset($_POST['join_course'])) { // Перевіряє, чи натиснута кнопка "приєднатись до курсу"
		$invite_code = $_POST['invite_code'];
			$result = $pdo->prepare("SELECT id FROM courses WHERE invite_code = :invite_code ");
			$params = [
			"invite_code" => $invite_code,
			]; 
			$result->execute($params);
			$row_count = $result->fetchColumn();
			if ($row_count != 0) {
			$course_id = $row_count;
				
				$result = $pdo->prepare("SELECT course_id FROM members WHERE user_id = :user_id ");
				$params = [
				"user_id" => $_SESSION['id'],
				]; 
				$result->execute($params);
				$row_count = $result->fetchAll();
				$coincience = true;
				for ($i=0; $i < count($row_count) ; $i++) { 
					if ($row_count[$i]['course_id'] == $course_id) $coincience = false;
				}
				if ($coincience) {
			$sql = "INSERT INTO `members` (`course_id`, `user_id`, `user_position`) VALUES (:course_id, :user_id, :user_position)";
    		$result = $pdo->prepare($sql);
			$params = [
			"course_id" => $course_id,
			"user_id" => $_SESSION['id'],
			"user_position" => 0,
		];
			$result->execute($params);
		} else echo 'Ви вже там';
		} else echo 'Курс відсутній';
	}


		
	



	//userCheck();
	updUserData($pdo);
	include '../data/libs/includes.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $coursesTitle; ?> - Knowledge</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>
	<div class="overlay" id="overlay" hidden></div>
	<div class="overlay" id="overlay1" hidden></div>

	<?php include '../data/libs/headerR.php' ?>

	<content>
		<div class="content">
			<div class="contentHeader">
				<div class="courseButtonContainer">
				<p class="courseButton" id="addButton" title="Створіть свій власний навчальний курс">
				<span class="material-icons courseBSpan" id="addSpan">&#xe145</span>
				<span>Створити курс</span>
				</p>
				<p class="courseButton" id="joinButton" title="Приєднайтесь до приватного курсу за допомогою коду, наданого вчителем">
				<span class="material-icons courseBSpan" id="joinSpan">&#xea4d</span>
				<span >Приєднатись до курсу</span>
				</p>
				</div>
			</div>
			

			<dialog class="createCourseFormDialog" open hidden>
				<div class="createCourseFormDialogContent" >

					<div class="createCourseFormHeader">
						<span class="material-icons" id="addSpan">&#xe145</span>
						<h3>Створення нового курсу</h3>
						<span class="material-icons" id="createCourseClose">&#xe5cd</span>
					</div>

					<div class="createCourseForm">
						<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
							<div class="createCourseFormInputs">
								<div class="input-group mb-2">
									<span class="input-group-text"><span class="material-icons" >title</span></span>
									<input class="form-control" type="text" class="createCourseInputText" placeholder="Назва курсу" required name="title">
								</div>
								<div class="input-group mb-2">
									<span class="input-group-text"><span class="material-icons-outlined" >category</span></span>
									<select name="category_id" class="form-select" required>
							  <option value="">Виберіть категорію</option>
							  <?php
							  		$result = $pdo->prepare("SELECT * FROM course_categories");

										$result->execute(); 
										$course_categories = $result->fetchAll();

							  foreach ($course_categories as $course_category => $value) {
							  ?>
							  <option value="<?php echo $value['id'] ; ?>"><?php echo $value['name'] ; ?></option>
							 <?php } ?>
							</select>
							<span class="disabled" style="font-size:14px; margin: 5px 0 0 2px;" >Назву та категорію курсу можна буде змінити згодом.<br>Також код приєднання буде відомий після створення курсу.</span>
								</div>
							</div>
							<input type="submit" name="create_course" class="btn btn-dark createButton" value="Створити курс">
						</form>
					</div>


				</div>
			</dialog>

			<dialog class="joinCourseFormDialog" open hidden>
				<div class="joinCourseFormDialogContent" >

					<div class="joinCourseFormHeader">
						<span class="material-icons" id="joinSpan">&#xea4d</span>
						<h3>Приєднання до курсу</h3>
						<span class="material-icons" id="joinCourseClose">&#xe5cd</span>
					</div>

					<div class="joinCourseForm">

						<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
							<div class="joinCourseFormInputs">
								<div class="input-group mb-2">
									<span class="input-group-text"><span class="material-icons" >vpn_key</span></span>
									<input type="text" class="form-control" autocomplete="off" placeholder="Код курсу" name="invite_code" required>
								</div>
								<span class="disabled" style="font-size:14px; margin: 5px 0 0 2px;" >Приєднуйтесь до курсу за допомогою коду наданого власником</span>
							</div>

							<input type="submit" name="join_course" class="btn btn-dark joinButton" value="Приєднатись до курсу">
						</form>
					</div>


				</div>
			</dialog>

			<?php
			  		$result = $pdo->prepare("SELECT * FROM members, courses WHERE members.user_id = :user_id AND members.course_id = courses.id");
			  			$params = [
			  				"user_id" => $_SESSION['id'],
			  			];
						$result->execute($params); 
						$courses = $result->fetchAll();
						if (count($courses) == 0) { ?>
							<div style="text-align: center; font-size:22px; margin-bottom: 20px;">
		      					<span class="material-icons" style="font-size:100px">&#xe5cd</span><br><p>Ви не приєднані ні до одного курсу</p>
		      				</div>
						<?php } else {
						for ($i=0; $i < count($courses); $i++) {

?>
						
						<div class="courseContainer" id="cC<?php echo $i; ?>">
							<a href="<?php echo $courses[$i]['id']; ?>"><div class="aDivCourse">
				<div class="courseHeader" <?php if ($courses[$i]['user_position'] == 1) { ?> style='background-color: var(--color-hover);' <?php } ?>>
					<span class="courseTitle"><?php echo $courses[$i]['title']; ?></span>
					
				</div>
				<div class="courseContent">


					<?php

										$result = $pdo->prepare("SELECT course_posts.text, course_posts.p_id, course_posts.test_post_id, course_posts.post_date, users.avatar, users.first_name, users.last_name, users.id FROM course_posts, users WHERE course_id = :course_id AND course_posts.user_id = users.id AND course_posts.post_date BETWEEN CURRENT_DATE()-3 AND CURRENT_DATE()+1 ORDER BY course_posts.post_date DESC");
										$params = [
										"course_id" => $courses[$i]['id'],
										]; 
										$result->execute($params);
										$coursePosts = $result->fetchAll();



										

										if(count($coursePosts) == 0) { ?>

											<p style="display:flex; align-items: center; font-size:14px"><span class="material-icons">self_improvement</span>Нових записів немає</p>

										<?php }
										else {
											$result = $pdo->prepare("SELECT * FROM tests");
											$params = [
											"course_id" => $courses[$i]['id'],
											]; 
											$result->execute($params);
											$tests = $result->fetchAll();

											foreach ($tests as $key => $test) {
												$allTests[$test['id']] = $test;
											}
										

										foreach ($coursePosts as $key => $post) {
		
			 ?>
			<div class="courseAMPContentStreamPost courseAMPPostSmall">
				<div class="streamPostHeader" style="display:inline-flex;" >

					<img src="<?php echo $post['avatar']; ?>">
					<div ><b><?php echo $post['first_name'].' '.$post['last_name']; ?></b><br>
					<span><?php echo $post['post_date']; ?></span></div>


					
				</div>
				<div class="streamPostContent" >
					<p><?php echo $post['text']; ?></p>
				</div>
				<?php if ($post['test_post_id'] != NULL) { ?>
				<a href="../tests/s/<?php echo $post['test_post_id'] ?>">
					<div class="streamPostTestPart" >
						<span class="material-icons-outlined testIcon" >note_alt</span>
						<span><?php echo $allTests[$post['test_post_id']]['name']; ?></span>
					</div>
				</a>
				<?php } ?>
			</div>
		<?php } } ?>



				</div>
			</div></a>
				<div class="courseContextMenuContainer" >
					<span class="material-icons more" onclick="courseMenu('<?php echo $i;?>')" >&#xe5d4</span>	
					</div>
					<div class="courseContextMenu" id="m<?php echo $i; ?>" hidden>
						<ol>
							<a href="<?php echo $courses[$i]['id']; ?>"><li class="courseContextMenuItem"><span class="material-icons-outlined" >&#xe8f4</span>Переглянути</li></a>
							<?php if ($courses[$i]['user_position'] == 1) { ?>
								<a href="index.php?delete_courseid=<?php echo $courses[$i]['id']; ?>"><li class="courseContextMenuItem" id="contextMenuItemRed" ><span class="material-icons-outlined" >&#xe872</span>Видалити курс</li></a>
							<?php } else {?>
							<a href="index.php?leave_courseid=<?php echo $courses[$i]['id']; ?>"><li class="courseContextMenuItem" id="contextMenuItemRed" ><span class="material-icons-outlined" >&#xeffc</span>Залишити курс</form></li></a>
						<?php }?>
						</ol>
					</div>
				
			</div>
			
			<?php } } ?>
			<script type="text/javascript">
				function courseMenu(i) {

				var m = document.querySelector('#m'+i);
				var overlay = document.querySelector('#overlay1');
				var cC = document.querySelector('#cC'+i);
		      	var pageHeight = document.documentElement.scrollHeight;
		      	overlay.style.height = String(pageHeight);

				
       
				if (m.hidden != true) {
					m.hidden = true;
					cC.style.zIndex = '';
					overlay.style.opacity = '0.0';
			        setTimeout(
		        	function(){
		        		overlay.hidden = true;
		        	}, 100);
			        console.log(overlay.hidden);
				}
				else {
				m.hidden = false;
				cC.style.zIndex = '5';
				overlay.hidden = false;
			        var timer = setTimeout((function(val){return function(){overlay.style.opacity = val};})('0.1'), 50);
				}
			    
			    document.querySelector(
			        '#overlay1'
			      ).onmouseover =  function () {
			        m.hidden = true;
			        overlay.style.opacity = '0.0';
			        cC.style.zIndex = '';
			        setTimeout(
		        	function(){
		        		overlay.hidden = true;
		        	}, 50);
			      }
				
	}

	</script>

			
		</div>
	
	<script type="text/javascript">
		const addButton = document.querySelector('#addButton');
		const joinButton = document.querySelector('#joinButton');
		const closeButton = document.querySelector('#createCourseClose');
		const closeButton2 = document.querySelector('#joinCourseClose');
		const createCourseFormDialog = document.querySelector('.createCourseFormDialog');
		const joinCourseFormDialog = document.querySelector('.joinCourseFormDialog');
		const overlay1 = document.querySelector('#overlay');
		addButton.addEventListener('click', function() {
			createCourseFormDialog.hidden = false;
			overlay1.hidden = false;
		        setTimeout(
		        	function(){
		        		overlay1.style.opacity = '0.5';
		        	}, 100);
		        setTimeout(
		        	function(){
		        		createCourseFormDialog.style.transform = 'scale(1)';
		        	}, 100);
				
		});
		joinButton.addEventListener('click', function() {
			joinCourseFormDialog.hidden = false;
			overlay1.hidden = false;
		        setTimeout(
		        	function(){
		        		overlay1.style.opacity = '0.5';
		        	}, 100);
		        setTimeout(
		        	function(){
		        		joinCourseFormDialog.style.transform = 'scale(1)';
		        	}, 100);
				
		});
		closeButton.addEventListener('click', closeCourseCreateForm);
		closeButton2.addEventListener('click', closeCourseJoinForm);
		overlay1.addEventListener('click', closeCourseCreateForm);
		overlay1.addEventListener('click', closeCourseJoinForm);
		function closeCourseCreateForm() {
			overlay1.style.opacity = '0.0';
			createCourseFormDialog.style.transform = 'scale(0)';
		        setTimeout(
		        	function(){
		        		overlay1.hidden = true;
		        	}, 500);
			
		}
		function closeCourseJoinForm() {
			overlay1.style.opacity = '0.0';
			joinCourseFormDialog.style.transform = 'scale(0)';
		        setTimeout(
		        	function(){
		        		overlay1.hidden = true;
		        	}, 500);
			
		}

	</script>
	<!--
	<footer class="footer">
		<p>Knowledge Inc. 2022</p>
	</footer>
	-->
</body>
</html>