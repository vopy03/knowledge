<?php

require_once "../dbconnect.php";
include '../data/libs/userCheck.php';

// $ip = $_SERVER['REMOTE_ADDR'];
// echo $ip;



include '../data/libs/includes.php';	
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $mainTitle; ?> - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>
	<div class="overlay" id="overlay1" hidden></div>
	<div class="overlay" id="overlay" <?php if (@$_SESSION['loginError'] == true) { ?> style="opacity: 0.1" <?php } else { ?> hidden <?php } ?>></div>
	
	<?php include '../data/libs/headerNR.php' ?>
	<content >
		<div class="contentHeader mainContentHeader">
					<div id="carouselExampleCaptions" class="carousel slide carousel-fade " data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../data/pics/scrn.png" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5><?php echo $mainCarouselDescTitle1; ?></h5>
        <p><?php echo $mainCarouselDesc1; ?></p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../data/pics/scrn2.png" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5><?php echo $mainCarouselDescTitle2; ?></h5>
        <p><?php echo $mainCarouselDesc2; ?></p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="../data/pics/scrn3.png" class="d-block" alt="...">
      <div class="carousel-caption d-none d-md-block">
        <h5><?php echo $mainCarouselDescTitle3; ?></h5>
        <p><?php echo $mainCarouselDesc3; ?></p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"  data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Предыдущий</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"  data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Следующий</span>
  </button>
</div>
			</div>
		<div class="content" >
			


			<div class="tests_content" >
				<div class="testsNavigation">
					<ul id="testsNavigationList">
			<a href="../courses"><li><span class="material-icons-outlined">school</span><?php echo $NLMyCourses ; ?></li></a>
			<a href="../tests"><li><span class="material-icons-outlined">note_alt</span><?php echo $NLTests ; ?></li></a>
					</ul>
				</div>
				<div class="testsTrueContent mainTrueContent">


					<h4><span class="material-icons-outlined">school</span><?php echo $MTCNewPostsInCTitle; ?></h4>
					<p class="disabled"  style="font-size: 14px;"><i><?php echo $MTCNewPostsInCDesc; ?></i></p>
					<hr>
					<div class="mainNewestCoursePosts" >


					<?php

					if(isset($_SESSION['login'])) {



			  		$result = $pdo->prepare("SELECT * FROM members, courses WHERE members.user_id = :user_id AND members.course_id = courses.id");
		  			$params = [
		  				"user_id" => $_SESSION['id'],
		  			];
					$result->execute($params); 
					$courses = $result->fetchAll();
					if (count($courses) == 0) { ?>

		      		<div style="text-align: center; font-size:22px; margin-bottom: 20px;">
		      				<span class="material-icons" style="font-size:100px">&#xe5cd</span><br><p><?php echo $MTCNewPostsInCNoOneC; ?></p>
		      		</div>

					<?php } else {
					for ($i=0; $i < count($courses); $i++) {
					?>
						
						<div class="courseMMContainer" id="cC<?php echo $i; ?>">
							<a href="../courses/<?php echo $courses[$i]['id']; ?>">
								<div class="aDivCourse">
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

											<p style="display:flex; align-items: center; font-size:14px"><span class="material-icons">self_improvement</span><?php echo $MTCNewPostsInCNoOnePost; ?></p>

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
						<span class="material-icons-outlined testIcon" >assignment</span>
						<span><?php echo $allTests[$post['test_post_id']]['name']; ?></span>
					</div>
				</a>
				<?php } ?>
			</div>
		<?php } } ?>



									</div>
								</div>
							</a>
						</div>
				
			
			<?php } } } else { ?>

				<div style="text-align: center; font-size:22px; margin-bottom: 20px;">
  				<span class="material-icons-outlined" style="font-size:100px">badge</span><br><p><?php echo $MTCNotReg; ?></p>
    		</div>

			<?php } ?>
				</div>
				<hr>

					<h4><span class="material-icons-outlined">history</span><?php echo $MTCRecentTestsTitle; ?></h4>
					<p class="disabled"  style="font-size: 14px;"><i><?php echo $MTCRecentTestsDesc; ?></i></p>
					<hr>
					<?php if(isset($_SESSION['login'])) { ?>
					<div class="testsMyRecentTestsList" id="tMRTL" >
						
						

						<table class="table table-borderless table-striped table-hover">
						  <thead>
						    <tr>
						    	<th></th>
						      <th scope="col"><?php echo $MTCRTTableTN; ?></th>
						      <th scope="col"><?php echo $MTCRTTableWC; ?></th>
						      <th scope="col"><?php echo $MTCRTTableNOQ; ?></th>
						      <th scope="col"><?php echo $MTCRTTableNOA; ?></th>
						      <th scope="col"><?php echo $MTCRTTableAWT; ?></th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php 


						  	$result = $pdo->prepare("SELECT tests.id, tests.name, users.first_name, users.last_name, tests.num_of_questions, test_members.remaining_attempts FROM test_members, tests, users WHERE test_members.member_name = :user_id AND test_members.test_id = tests.id AND tests.creator_id = users.id");
								$params = [
									"user_id" => $_SESSION['full_name'],
								];
								$result->execute($params); 
								$recent_tests = $result->fetchAll();



						  	foreach ($recent_tests as $recent_test => $test) {
						  	 ?>
						    <tr>
						    	<th><span class="material-icons-outlined" >assignment</span></th>
						      <td scope="row">
						      	<a href="../tests/s/<?php echo $test['id']; ?>">
						      		<p><?php echo $test['name']; ?></p>
						      	</a>
						      </td>
						      <td scope="row"><p><?php echo $test['first_name'].' '. $test['last_name'] ; ?></p></td>
						      <td><?php echo $test['num_of_questions']; ?></td>
						      <td><?php echo $test['remaining_attempts']; ?></td>
						      <td>
						      	<a href="../tests/vr/<?php echo $test['id'] ?>">
							      	<div class="testsMyTestsAction rounded" title="<?php echo $MTCRTTableActTitle; ?>">
							      		<span class="material-icons-outlined" >preview</span>
							      	</div>
						      	</a>
						      	
						      </td>
						    </tr>
						  <?php }  ?>
						  </tbody>
						</table>
					</div>
				<?php } else { ?>
					<div style="text-align: center; font-size:22px; margin-bottom: 20px;">
  					<span class="material-icons-outlined" style="font-size:100px">badge</span><br><p><?php echo $MTCNotReg; ?></p>
    			</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</content>
	<script type="text/javascript">
		// window.onmouseover = function () {
		// 	console.log(window.screen.width);
		// }
	</script>
</body>
</html>