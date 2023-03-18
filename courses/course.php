<?php
require_once "../dbconnect.php";
include '../data/libs/userCheck.php';
	userCheck();

	// echo '<pre>';
	// var_dump($_POST);
	// echo '</pre>';

				
	$result = $pdo->prepare("SELECT * FROM members WHERE user_id = :user_id AND course_id = :course_id ");
	$params = [
	"user_id" => $_SESSION['id'],
	"course_id" => $_GET['courseId'],
	]; 
	$result->execute($params);
	$row_count = $result->fetchColumn();
	$coincience = true;

	if ($row_count == 0) {
		$coincience = false;
		if($_GET['courseId'] != 'NotFound') header('Location: ../courses/NotFound');
	}
	else {
		$result = $pdo->prepare("SELECT * FROM courses WHERE id = :course_id ");
		$params = [
		"course_id" => $_GET['courseId'],
		]; 
		$result->execute($params);
		$course = $result->fetchAll();
	} 
	if (isset($_POST['changeCourseSettings'])) {

		if (!isset($_POST['allow_post'])) {
			$_POST['allow_post'] = 1;
		} else {
			$_POST['allow_post'] = 0;
		}

		if ($course[0]['title'] == $_POST['title'] && $course[0]['category_id'] == $_POST['category_id'] && $course[0]['allow_post'] == $_POST['allow_post'] ) {
			// echo "Дані ті самі";
			header('Location: ../courses/'.$_GET['courseId']);
		}
		else {


		$sql = "UPDATE `courses` SET `title` = :title, `category_id` = :category_id, `allow_post` = :allow_post  WHERE `id` = :id";
			$params = [
			"title" => $_POST['title'],
			"category_id" => $_POST['category_id'],
			"allow_post" => $_POST['allow_post'],
		    "id" => $_GET['courseId'],
			];
			$prepare = $pdo -> prepare($sql);
			$prepare -> execute($params);


			header('Location: ../courses/'.$_GET['courseId']);

		}
	}



	if (isset($_POST['publish_post'])) {

		$is_test = false;
		$test_post_id = NULL;
		if (isset($_POST['testId'])) {
			$is_test = true;
			$test_post_id = $_POST['testId'];
		}
		

		$sql = "INSERT INTO `course_posts` (`course_id`, `user_id`, `text`, `post_date`, `is_test`, `test_post_id`) VALUES (:course_id, :user_id, :text, :post_date, :is_test, :test_post_id )";

	    $result = $pdo->prepare($sql);
	    $params = [
	    "course_id" => $_GET['courseId'],
	    "user_id" => $_SESSION['id'],
	    "text" => $_POST['post_text'] ,
	    "post_date" => date('Y.m.d H:i:s'),
	    "is_test" => $is_test,
	    "test_post_id" => $test_post_id,
	    ]; 
	    $result->execute($params);

	    header('Location: ../courses/'.$_GET['courseId']);
	}

	if (isset($_POST['delete_post'])) {

		$sql = "DELETE FROM course_posts WHERE p_id = :p_id";

		$statement = $pdo->prepare($sql);
		$statement->bindParam(':p_id', $_POST['p_id'], PDO::PARAM_INT);
		$statement->execute();

		header('Location: ../courses/'.$_GET['courseId']);
	}

	$result = $pdo->prepare("SELECT user_position FROM members WHERE user_id = :user_id AND course_id = :course_id");
	$params = [
	"course_id" => $_GET['courseId'],
	"user_id" => $_SESSION['id'],
	]; 
	$result->execute($params);
	$user_position = $result->fetchAll();
	$user_position = $user_position[0][0];




	//userCheck();
	updUserData($pdo);
	include '../data/libs/includes.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $course[0]['title']; ?> - Knowledge</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>
	<div class="overlay" id="overlay" hidden></div>
	<div class="overlay" id="overlay1" hidden></div>

	<?php include '../data/libs/headerR.php' ?>

	<content>
		<div class="contentCourse">
			<div class="contentHeaderBackground">

				<div class="contentHeaderContainer">

					<div class="contentHeader">
						<div class="course404ButtonContainer">
							<a href="../courses">
								<p class="courseButton">
									<span class="material-icons courseBSpan">&#xe5c4</span>
									<span>Повернутись назад</span>
								</p>
							</a>
						</div>
					</div>

				

			<?php if($coincience) { ?>

				
					<div class="courseOnPageHeader">
						<h3><?php echo $course[0]['title']; ?></h3>
						<span onclick="copy_data('courseInviteCode')" title="Натисніть щоб скопіювати" > <span class="badge bg-dark" id="courseInviteCode"  ><?php echo $course[0]['invite_code']; ?></span></span>
						<div class="courseOnPageNavigation"> 
							<ul id="courseList">
							<li class="courseListItem selected" id="mainInCourse"><span class="material-icons-outlined CLIcon">school</span><span class="courseListItemTitle">Головна</span></li>
							<li class="courseListItem"><span class="material-icons-outlined CLIcon">note_alt</span><span class="courseListItemTitle">Тести</span></li>
							<li class="courseListItem"><span class="material-icons-outlined CLIcon">group</span><span class="courseListItemTitle">Учасники</span></li>
							</ul>
						</div>
						<?php if ($user_position == 1) { ?>
						<span class="material-icons" id="courseSettings"data-bs-toggle="modal" data-bs-target="#changeCourseSettings" >settings</span>

						<div class="modal fade " id="changeCourseSettings" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
						  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">settings</span>Налаштування</h4>
						        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						      </div>
						      <div class="modal-body">
						      	<h5>Основні</h5>
						      	<form method="POST" action="../courses/<?php echo $_GET['courseId']; ?>">
						      		<p class="form-check form-switch" >
				                        <input type="checkbox" class="form-check-input" name="allow_post" id="courseAllow" <?php if ($course[0]['allow_post'] == 0){ echo "checked"; } ?>>
				                        <label for="courseAllow" class="form-check-label">Заборонити учасникам створювати нові записи
				                            <br>
				                            <span class="testCreateHint disabled" style="font-size: 12px;">Учасники більше не зможуть додавати нові записи</span>
				                        </label>
				                        
				                    </p>
				                    <br>
				                    <h5>Зміна даних</h5>
							<!-- <span class="disabled" style="font-size:14px; margin: 5px 0 0 2px;" >Назву та категорію курсу можна буде змінити згодом.<br>Також код приєднання буде відомий після створення курсу.</span> -->
						      		<div class="input-group mb-2">
									<span class="input-group-text"><span class="material-icons" >title</span></span>
									<input class="form-control" type="text" value="<?php echo $course[0]['title']; ?>" class="createCourseInputText" placeholder="Назва курсу" required name="title">
								</div>
								<div class="input-group mb-2">
									<span class="input-group-text"><span class="material-icons-outlined" >category</span></span>
									<select name="category_id" value='<?php echo $course[0]['category_id']; ?>' class="form-select" required>
							  <option value="">Виберіть категорію</option>
							  <?php
							  		$result = $pdo->prepare("SELECT * FROM course_categories");

										$result->execute(); 
										$course_categories = $result->fetchAll();

							  foreach ($course_categories as $course_category => $value) {
							  ?>
							  <option value="<?php echo $value['id'] ; ?>" <?php if($course[0]['category_id'] == $value['id'] ) {echo "selected";} ?> ><?php echo $value['name'] ; ?></option>
							 <?php } ?>
							</select>
								</div>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
						        <input type="submit" name="changeCourseSettings" class="btn btn-dark" value="Зберегти зміни">
						        </form>

						      </div>
						    </div>
						  </div>
						</div>
					<?php } ?>
					</div>
				<div class="courseOnPageContent">


				<div class="tabContent"></div>
				<div class="tabContent"></div>
				<div class="tabContent"></div>


				</div>
				<script type="text/javascript">

					function copy_data(el) {
					    var range = document.createRange();
					    var elem = document.getElementById(el)
					    range.selectNode(elem);
					    window.getSelection().removeAllRanges();
					    window.getSelection().addRange(range);
					    document.execCommand("copy");
					    window.getSelection().removeAllRanges();

					    elem.style.borderColor = 'var(--color-alert-success)';
					    elem.style.borderStyle = 'solid';
					    elem.style.borderWidth = '2px';
					    setTimeout((function(val){return function(){elem.style.borderWidth = val};})('0px'), 500);
					    // console.log(elem.style);
					}

					var tab;
					var tabContent;

					tabContent = document.getElementsByClassName('tabContent');
						tab = document.getElementsByClassName('courseListItem');
						tabIcon = document.getElementsByClassName('CLIcon');
						tabTitle = document.getElementsByClassName('courseListItemTitle');	
						hideTabsContent(1);
						showTabsContentAJAX(0);
					function hideTabsContent(a) {
						for (var i = a; i < tabContent.length; i++) {
							tabContent[i].classList.remove('show');
							tabContent[i].classList.add('hide');
							tab[i].classList.remove('selected');
						}
					}
					document.getElementById('side-panel').onclick = function (event) {
						var target = event.target;
						// console.log(target.classList.contains('CLIcon'));
						if (target.className == 'courseListItem' || target.classList.contains('CLIcon') || target.classList.contains('courseListItemTitle')) {
							for(var i=0; i < tab.length; i++) {
								if (target==tab[i] || target==tabIcon[i] || target==tabTitle[i]) {
									showTabsContent(i);
									break;
								}
							}
						}
					}
					function showTabsContent(b) {
						if (tabContent[b].classList.contains('hide')) {
							hideTabsContent(0);
							tab[b].classList.add('selected');
							tabContent[b].classList.remove('hide');
							tabContent[b].classList.add('show');
							showTabsContentAJAX(b);
						}
					}
					function showTabsContentAJAX(n) {
						$.ajax({
								type: "POST",
								url: "courseContent.php",
								data: "value="+ n +"&courseId=<?php echo $_GET['courseId'] ?>",
								success: function(html){
									$(tabContent[n]).html(html);
							   }
							});
					}
				</script>

			<?php } else { ?>


				<div class="course404"><span class="material-icons">&#xe5cd</span><br><p>Такого курсу не знайдено!</p></div>


			<?php } ?>
			</div>

			</div>
		</div>
	</content>
	
</body>
</html>