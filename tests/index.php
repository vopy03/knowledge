<?php
require_once "../dbconnect.php";
include '../data/libs/userCheck.php';
	userCheck();

	

if (isset($_GET['testDeleteId'])) {
	
	$deleteID = $_GET['testDeleteId'];

	$result = $pdo->prepare("SELECT name,filepath FROM tests WHERE id = :test_id");
	$result->bindParam(':test_id', $deleteID, PDO::PARAM_INT);
	$result->execute(); 
	$test = $result->fetchAll();
	$testPath = $test[0][1];
	remDir(substr($testPath, 0, (strlen($testPath)-strlen('/'.$test[0][0].'.kntf'))));
	

	$result = $pdo->prepare("SELECT tm_id FROM test_members WHERE test_id = :test_id");
	$params = [
	"test_id" => $deleteID,
	]; 
	$result->execute($params); 
	$delMems = $result->fetchAll();


	 


	foreach ($delMems as $key => $value) {
	$sql = "DELETE FROM test_results WHERE test_member_id = :test_member_id";

	$statement = $pdo->prepare($sql);
	$params = [
	"test_member_id" => $value[0],
	];
	$statement->execute($params);
	}

	$sql = "DELETE FROM test_members WHERE test_id = :test_id";

	$statement = $pdo->prepare($sql);
	$params = [
	"test_id" => $deleteID,
	];
	$statement->execute($params);

	$sql = "DELETE FROM tests WHERE id = :test_id";

	$statement = $pdo->prepare($sql);
	$params = [
	"test_id" => $deleteID,
	];
	$statement->execute($params);

	
	header('Location: ../tests');
}


if (isset($_GET['phpS'])) {
    $testName = $_GET['testName'];
    mkdir("../data/tests/".$_SESSION['login']."/".$testName."/test_results", 0777, true);
    $filepath = "../data/tests/".$_SESSION['login']."/". $testName."/".$testName.".kntf";

		$fd = fopen($filepath, 'w+') or die("не удалось создать файл");

		$str = $_GET['phpS'];
		$str = str_replace("<br>", "\n", $str);
		fwrite($fd, $str);
		fclose($fd);

    $sql = "INSERT INTO `tests` (`name`, `creator_id`, `date_of_creating`, `num_of_questions`, `filepath`) VALUES (:name, :creator_id, :date_of_creating, :num_of_questions, :filepath)";

    $result = $pdo->prepare($sql);
    $params = [
    "name" => $testName,
    "creator_id" => $_SESSION['id'],
    "date_of_creating" => date('Y.m.d'),
    "num_of_questions" => $_GET['qcount'],
    "filepath" => $filepath,
    ]; 
    $result->execute($params);


    header('Location: ../tests');

}

$result = $pdo->prepare("SELECT * FROM tests WHERE creator_id = :user_id");
$result->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
$result->execute(); 
$user_tests = $result->fetchAll();

$result = $pdo->prepare("SELECT tests.id, tests.name, users.first_name, users.last_name, tests.num_of_questions, test_members.remaining_attempts FROM test_members, tests, users WHERE test_members.member_name = :user_id AND test_members.test_id = tests.id AND tests.creator_id = users.id");
$params = [
	"user_id" => $_SESSION['full_name'],
];
$result->execute($params); 
$recent_tests = $result->fetchAll();
  		


//userCheck();
updUserData($pdo);
include '../data/libs/includes.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Тести - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>

	<div id="overlayNone" hidden></div>

	<div class="mHFixed">
	<?php include '../data/libs/headerR.php' ?>
	</div>

	<content>
		<div class="content" >
			<div class="contentHeader cHFixed">
				<a href="../main">
					<p class="courseButton">
						<span class="material-icons courseBSpan">&#xe5c4</span>
						<span>Повернутись назад</span>
					</p>
				</a>
			</div>


			<div class="tests_content" >
				<div class="testsNavigation tNFixed">
					<ul id="testsNavigationList">
						<!--<li><span class="material-icons" >add</span>Створити тест</li> -->
						<li><span class="material-icons-outlined">assignment_ind</span><?php echo 'Мої тести' ; ?></li>
						<a href="#tMRTL"><li><span class="material-icons-outlined">history</span><?php echo 'Нещодавні тести' ; ?></li></a>
					</ul>
				</div>
				<div class="testsTrueContent tTCFixed">
						<h3><span class="material-icons-outlined">assignment_ind</span>Мої тести</h3>
					<div class="testsMyTestsList">
						

						<table class="table table-borderless table-striped table-hover">
						  <thead>
						    <tr>
						    	<th></th>
						      <th scope="col">Назва тесту</th>
						      <th scope="col">Дата створення</th>
						      <th scope="col">Кількість питань</th>
						      <th scope="col">Користувачів пройшло</th>
						      <th scope="col">Дії з тестом</th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php foreach ($user_tests as $user_test => $test) {
						  	 ?>
						   
						    	<tr>

						    	<th><span class="material-icons-outlined" >assignment</span></th>
						      <td scope="row"><a href="s/<?php echo $test['id']; ?>"><p><?php echo $test['name']; ?></p></a></td>
						      <td><?php echo $test['date_of_creating']; ?></td>
						      <td><?php echo $test['num_of_questions']; ?></td>
						      <td>
						      	<?php 
						      	$result = $pdo->prepare("SELECT COUNT(*) FROM test_members WHERE test_id = :test_id");
							  		$result->bindParam(':test_id', $test['id'], PDO::PARAM_INT);
										$result->execute(); 
										$num = $result->fetchAll();
						      	echo $num[0][0]; 
						      	?>
						    	</td>
						      <td>
						      	<div>
						      		<span class="material-icons-outlined" id='tAM<?php echo $test['id']; ?>' onclick="openActions('tAD<?php echo $test['id']; ?>')">more_vert</span>
						      		<div id='tAD<?php echo $test['id']; ?>' class='testActionDiv' hidden>


								      	<a href="vr/<?php echo $test['id']; ?>"><div class="testsMyTestsAction rounded" title="Переглянути результати">
								      		<span class="material-icons-outlined" >preview</span>
								      	</div></a>
								      	<a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?testDeleteId=<?php echo $test['id']; ?>"><div class="testsMyTestsAction rounded" title="Видалити">
								      		<span class="material-icons-outlined" >delete</span>
								      	</div></a>
								      	<a href="<?php echo $test['filepath']; ?>" download>
								      		<div class="testsMyTestsAction rounded" title="Завантажити">
								      			<span class="material-icons" >download</span>
						      				</div>
						      			</a>


						      	</div>
						      	</div>
						      </td>
						    </tr>
						    <?php	} ?>
						  </tbody>
						</table>
						<script type="text/javascript">
							var testActDivs = document.getElementsByClassName('testActionDiv');
		      			function openActions(div) {
		      				var testActionDiv = document.getElementById(div);
		      				if(testActionDiv.hidden == true) {
		      					testActionDiv.hidden = false;
		      					testActionDiv.style.opacity = 0;
		      					setTimeout((function(val){return function(){testActionDiv.style.opacity = val};})('1'), 100);
		      				}

		      			}
		      			function closeAction(div) {
		      				var testActionDiv = document.getElementById(div);
		      					testActionDiv.hidden = true;
		      			}
		      			window.onclick = function(e) {
		      				//console.log(e.target.id.substring(3));
		      				for (var i = testActDivs.length - 1; i >= 0; i--) {
		      					testActDivs[i].hidden = true;
		      					if (testActDivs[i].id.substring(3) == e.target.id.substring(3)) {
		      						testActDivs[i].hidden = false;
		      					}
		      				}
		      			}
						</script>

						<div class="createTestButton" style="display:inline-flex;" >
							<form id="uploadTestForm" enctype="multipart/form-data">
							<input type="file" id='uploadTest' accept=".kntf" name="testUpload" hidden>
							</form>
							<label for="uploadTest">
							<p class="courseButton rounded" >
							<span class="material-icons">upload</span>
							<span>Завантажити тест</span>
							</p>
							</label>
							<div id="addButton" >
								<p class="courseButton rounded">
								<span class="material-icons">add</span>
								<span>Створити тест</span>
								</p>
							</div>
						</div>

					</div>
					<dialog class="createTestFormDialog" open hidden>
						<div class="createTestFormDialogContent" >

							<div class="createTestFormHeader">
								<span class="material-icons" id="addSpan">&#xe145</span>
								<h3>Створення нового тесту</h3>
								<span class="material-icons" id="createTestClose">&#xe5cd</span>
							</div>

							<div class="createTestForm">
								<?php include 'pages/testCreate.php' ?>
							</div>

							<label for="subTestCreate" class="btn btn-dark subTestCreate">Створити тест</label>

						</div>

					</dialog>
						<h3><span class="material-icons-outlined">history</span>Нещодавні тести</h3>
					<div class="testsMyRecentTestsList" id="tMRTL" >
						

						<table class="table table-borderless table-striped table-hover">
						  <thead>
						    <tr>
						    	<th></th>
						      <th scope="col">Назва тесту</th>
						      <th scope="col">Хто створив</th>
						      <th scope="col">Кількість питань</th>
						      <th scope="col">Спроб залишилось</th>
						      <th scope="col">Дії з тестом</th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php foreach ($recent_tests as $recent_test => $test) {
						  	 ?>
						    <tr>
						    	<th><span class="material-icons-outlined" >assignment</span></th>
						      <td scope="row">
						      	<a href="s/<?php echo $test['id']; ?>">
						      		<p><?php echo $test['name']; ?></p>
						      	</a>
						      </td>
						      <td scope="row"><p><?php echo $test['first_name'].' '. $test['last_name'] ; ?></p></td>
						      <td><?php echo $test['num_of_questions']; ?></td>
						      <td><?php echo $test['remaining_attempts']; ?></td>
						      <td>
						      	<a href="vr/<?php echo $test['id'] ?>">
							      	<div class="testsMyTestsAction rounded" title="Переглянути результати">
							      		<span class="material-icons-outlined" >preview</span>
							      	</div>
						      	</a>
						      	</div>
						      </td>
						    </tr>
						  <?php } ?>
						  </tbody>
						</table>

					</div>



				</div>
			</div>
			<!--
			<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Переключатель справа offcanvas</button>

<div class="offcanvas offcanvas-end redpls" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Offcanvas справа</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"><span class="material-icons-outlined">close</span></button>
  </div>
  <div class="offcanvas-body">
    <p>Здарова</p>
  </div>
</div> -->


		</div>

	</content>
	<script type="text/javascript">
		const addButton = document.querySelector('#addButton');
		const closeButton = document.querySelector('#createTestClose');
		const createTestFormDialog = document.querySelector('.createTestFormDialog');
		const overlay1 = document.querySelector('#overlayNone');
		addButton.addEventListener('click', function() {
			createTestFormDialog.hidden = false;
			overlay1.hidden = false;
		        setTimeout(
		        	function(){
		        		overlay1.style.opacity = '0.5';
		        	}, 100);
		        setTimeout(
		        	function(){
		        		createTestFormDialog.style.transform = 'scale(1)';
		        	}, 100);
				
		});
		document.addEventListener('keydown', function(event) {
		  if (event.code == 'Escape') {
		    closeTestCreateForm();
		  }
		});
		closeButton.addEventListener('click', closeTestCreateForm);
		overlay1.addEventListener('click', closeTestCreateForm);
		function closeTestCreateForm() {
			overlay1.style.opacity = '0.0';
			createTestFormDialog.style.transform = 'scale(0)';
		        setTimeout(
		        	function(){
		        		overlay1.hidden = true;
		        	}, 500);
			
		}

		$(document).ready(function(){

		    $("#uploadTestForm").change(function(){

		        var fd = new FormData();
		        var files = $('#uploadTest')[0].files;
		        
		        // Check file selected or not
		        if(files.length > 0 ){
		           fd.append('file',files[0]);

		           $.ajax({
		              url: '../data/libs/uploadTest.php',
		              type: 'post',
		              data: fd,
		              contentType: false,
		              processData: false,
		              success: function(html){
								$("#content").html(html);
								setTimeout( function() {location.reload();}, 500);
								
						   }
		           });
		        }
		    });
		});

	</script>
	<div id="content" ></div>
</body>
</html>