<?php
require_once "../dbconnect.php";
include '../data/libs/userCheck.php';
	userCheck();


	$ok = false;
	$formatoNotOk = false;
	$defaultAvatarPath = '../data/pics/default-icons/user.svg';

	$result = $pdo->prepare("SELECT avatar FROM `users` WHERE id = :id");
	$params = [
	"id" => $_SESSION['id'],
	]; 
	$result->execute($params); 
	$result = $result->fetchAll();
	$userAvatarPath = $result[0][0];


	$result = $pdo->prepare("SELECT `ch_avatar_date`, avatar FROM `users` WHERE id = :id AND `ch_avatar_date` BETWEEN 0 AND CURRENT_DATE()-1");
	$params = [
	"id" => $_SESSION['id'],
	]; 
	$result->execute($params); 
	$result = $result->fetchAll();

	@$ch_avatar_date = $result[0][0];

	if($ch_avatar_date) $ok = true;


if(isset($_FILES['avatarUpload'])) {

	$fileformat = substr($_FILES['avatarUpload']['name'], strlen($_FILES['avatarUpload']['name'])-4,strlen($_FILES['avatarUpload']['name']));

	if(str_contains($fileformat, 'png') || str_contains($fileformat, 'jpg') || str_contains($fileformat, 'jpeg')) $ok = true;
	else {
		$ok = false;
		$formatoNotOk = true;
	}
	

	if($ok) {

		$picPath = $_FILES['avatarUpload']['tmp_name'];
		$endPath = '../data/pics/avatars/'.$_SESSION['login'];
		$endPathToFile = $endPath . '/avatar_150x150.png';
		// echo '<pre>';
		// echo $picPath;
		// echo '<br>';
		// echo $endPath;
		// echo '</pre>';

		mkdir($endPath, 0777, true);
		resizePicNSave('YpmZ7xx1dLf4WCFJd07y754W0x6nHGPt', $picPath, 150, 150, $endPathToFile );

		$sql = "UPDATE `users` SET `ch_avatar_date` = CURRENT_DATE(), `avatar` = :avatar  WHERE `id` = :id";
		$params = [
	    "id" => $_SESSION['id'],
	    "avatar" => $endPathToFile,
		];
		$prepare = $pdo -> prepare($sql);
		$prepare -> execute($params);

		header('Location: http:../settings');
	}
}
if(isset($_POST['avatarRemove'])) {
	remDir('../data/pics/avatars/'.$_SESSION['login']);
	$sql = "UPDATE `users` SET `avatar` = :avatar  WHERE `id` = :id";
		$params = [
	    "id" => $_SESSION['id'],
	    "avatar" => $defaultAvatarPath,
		];
		$prepare = $pdo -> prepare($sql);
		$prepare -> execute($params);
		header('Location: http:../settings');
}


if(isset($_POST['updateUserData'])) {

		$login = $_POST['login'];

		$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
		$params = [
		"login" => $login,
		]; 
		$result->execute($params); 
		$row_count = $result->fetchAll();
		$row_count = count($row_count);
		if ($row_count == 0 || $login == $_SESSION['login']) {
		$wrongName = false;


		$sql = "UPDATE `users` SET `login` = :login, `first_name` = :first_name, `last_name` = :last_name, `email` = :email  WHERE `id` = :id";
			$params = [
			"login" => $login,
			"first_name" => $_POST['first_name'],
			"last_name" => $_POST['last_name'],
			"email" => $_POST['email'],
		    "id" => $_SESSION['id'],
			];
			$prepare = $pdo -> prepare($sql);
			$prepare -> execute($params);

			$_SESSION['login'] = $_POST['login'];

			$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
			$params = [
			"login" => $_SESSION['login'],
			]; 
			$result->execute($params); 
			$user = $result->fetchAll();

			//header('Location: http:../settings');

		} else $wrongName = true;

}

if (isset($_POST['updateUserPassword'])) {

			$login = filter_var(trim($_SESSION['login']), FILTER_SANITIZE_STRING);
	    	$oldpass = filter_var($_POST['oldpass'], FILTER_SANITIZE_STRING);
	    	$newpass = filter_var($_POST['newpass'], FILTER_SANITIZE_STRING);

	    	$result = $pdo->prepare("SELECT password FROM users WHERE login = :login");
			$params = [
			"login" => $login,
			]; 
			$result->execute($params);
			$result = $result->fetchAll();

			@$pp = $result[0]['password'];

			if (password_verify($oldpass, $pp)) {
				$oldpassVerify = true;
				// зміна паролю
				$sql = "UPDATE `users` SET `password` = :password WHERE `login` = :login";

	    		$result = $pdo->prepare($sql);
				$params = [
				"login" => $login,
				"password" => password_hash($newpass, PASSWORD_DEFAULT),
				]; 
				$result->execute($params);

				//header('Location: http:../settings');
			} else $oldpassVerify = false;
	}
updUserData($pdo);
include '../data/libs/includes.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $settingsTitle; ?> - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>

	<div id="overlayNone" hidden></div>


	<?php include '../data/libs/headerR.php' ?>

	<content>
		<div class="content" >

			<div class="tests_content" >
				<div class="testsNavigation">
					<div class="testsNavigationHeader">
					<h4>Налаштування</h4>
					<hr>
					</div>
					<ul id="testsNavigationList" class="settingsNavigationList">
						<a href="#aS">
						<li>
							<span class="material-icons">settings</span>
							<span class="testsNavigationLabel"><?php echo 'Загальні' ; ?></span>

							<div class="navigationSubItem">
									<a href="#gSView">
										<span class="material-icons">format_paint</span>
										<span><?php echo 'Зовнішній вигляд' ; ?></span>
									</a>
									
							</div>
<!-- 							<div class="navigationSubItem">
								<a href="#gSLang">
									<span class="material-icons">language</span>
									<span><?php echo 'Мова' ; ?></span>
								</a>
							</div> -->

						</li>
					</a>
							
						<a href="#aS">
							<li>
								<span class="material-icons">person</span>
								<span class="testsNavigationLabel"><?php echo 'Акаунт' ; ?></span>
										
								<div class="navigationSubItem">
									<a href="#aSEditData">
										<span class="material-icons">edit</span>
										<span><?php echo 'Зміна даних' ; ?></span>
									</a>
								</div>

								<div class="navigationSubItem">
									<a href="#aSEditPass">
										<span class="material-icons">key</span>
										<span><?php echo 'Зміна паролю' ; ?></span>
									</a>
								</div>

								<div class="navigationSubItem">
									<a href="#aSDelete">
										<span class="material-icons">delete_forever</span>
										<span><?php echo 'Видалення акаунту' ; ?></span>
									</a>
								</div>
							</li>
						</a>
						

					</ul>
				</div>
				<div class="testsTrueContent">
					<h3 id='gS'>
						<span class="material-icons">settings</span>Загальні
					</h3>
					<div class="settingContent">
						<div class="settingSubContent">
							<h5 id='gSView'><span class="material-icons">format_paint</span>Зовнішній вигляд
							</h5>

							<p class="form-check form-switch" id="fel" >
                        <input type="checkbox" class="form-check-input" id="key2" name="testMLCSettingCheckbox" id="testTimerSettingCheckbox" <?php if (@$_SESSION['dark'] == 1) { ?> checked <?php } ?>>
                        <label for="key2" class="form-check-label">Темний режим
                        </label>
                        <script type="text/javascript">
                        	$(document).ready(function(){
		
								$('#key2').change(function(){
									$.ajax({
										url: "../data/libs/changeTheme.php",
										success: function(html){
											$("#content").html(html);
										}
									});
									return false;
								});
								$('#fel').click(function(){
									$.ajax({
										url: "../data/libs/changeTheme.php",
										success: function(html){
											$("#content").html(html);
										}
									});
									return false;
								});

							});

							var e2 = document.querySelector('#key2');
							var fel = document.querySelector('#fel');

							e2.onclick = function() {
					  		document.body.classList.toggle('dark_mode');
					  		el.checked = (el.checked != true) ? true : false;
							}
							fel.onclick = function() {
								e2.checked = (e2.checked != true) ? true : false;
								document.body.classList.toggle('dark_mode');
					  			el.checked = (el.checked != true) ? true : false;
							}	


                        </script>
                        
                    </p>

						</div>
						<!-- <div class="settingSubContent">
							<h5 id='gSLang'><span class="material-icons">language</span>Мова
							</h5>

							<select class="form-select" style="width:300px;">
							  <option selected>Українська</option>
							  <option value="1">English</option>
							</select>

						</div> -->
					</div>
					<h3 id='aS'><span class="material-icons">person</span>Акаунт</h3>
					<div class="settingContent">
						<div class="settingSubContent">
							<h5 id='aSEditData'><span class="material-icons">edit</span>Зміна даних
							</h5>
							<?php if (isset($_POST['updateUserData']) && !$wrongName) {
							?>
							<div class="alert alert-success settingsAccountAlert sAASuccess" role="alert">
							  <span class="material-icons">done</span>
							  <span>Дані успішно змінено!</span>
							</div>
							<?php } if (isset($_POST['updateUserData']) && $wrongName) {
							?>
							<div class="alert alert-success settingsAccountAlert sAAFail" role="alert">
							  <span class="material-icons">close</span>
							  <span>Даний логін зайнятий!</span>
							</div>
							<?php }  ?>

							<?php if($formatoNotOk) { ?>
							<div class="alert alert-dark sAAFail" style="display:flex; align-items:center; padding: 7px; border:none; gap:5px; justify-content:center;" ><span class="material-icons-outlined" >warning_amber</span><span>Невірний формат фото</span></div>
							<?php } ?>

							<div class="settingSubContentContent">


								<div class="settingsUserAvatar">

									<img width="125px" src="<?php echo $_SESSION['avatar'] ?>">	
									<div class="userAvatarButtons">	
										<label class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#changeAvatar"><span class="material-icons-outlined" >image</span><span>Нове фото</span></label>

										<div class="modal fade " id="changeAvatar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
										  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
										    <div class="modal-content">
										      <div class="modal-header">
										        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons-outlined" >image</span><span>Нове фото</span></h4>
										        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										      </div>
										      <div class="modal-body">
										      	<div style="margin-bottom:15px">
										      		<div class="alert alert-dark">
										      	<span class="chAvatarHintTitle"><span class="material-icons-outlined" >info</span> <i>Після завантаження, фотографія буде обрізана квадратом 150x150 пікселів. Формати що підтримуються: PNG, JPG, JPEG<br></i></span></div>
										      	<div class="alert alert-warning">
										      		<p><span class="chAvatarHintTitle"><span class="material-icons-outlined" >warning_amber</span> <i>УВАГА! Фото можна змінити один раз на день!</i></span></p>
										      	</div>
										      	<?php if(!$ok) { ?>
										      	<div class="alert alert-danger">
										      		<p><span class="chAvatarHintTitle"><span class="material-icons-outlined" >cancel</span><?php echo "Ви сьогодні вже змінювали фото. Спробуйте завтра"; ?></span></p>
										      	</div>
										      	<?php } ?>
										      	</div>
										      	<form id="uploadTestForm" method="POST" action="../settings/index.php" enctype="multipart/form-data">
										      		<div class="input-group mb-3">
															  <input type="file" class="form-control" id='uploadAvatar' accept="image/png, image/jpeg, image/jpg" name="avatarUpload" <?php if(!$ok) echo "disabled"; ?>>
															</div>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
										        <input type="submit" name="updateAvatar" class="btn btn-dark" value="Зберегти" <?php if(!$ok) echo "disabled"; ?>>
										        </form>

										      </div>
										    </div>
										  </div>
										</div>



										<button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#avatarRemove" <?php if($userAvatarPath == $defaultAvatarPath) echo "disabled"; ?>><span class="material-icons-outlined" style="color:white;">delete</span>Видалити</button>

										<div class="modal fade " id="avatarRemove" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
										  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
										    <div class="modal-content">
										      <div class="modal-header">
										        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons-outlined">delete</span>Видалення фото</h4>
										        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										      </div>
										      <div class="modal-body">
										      	<div class="alert alert-warning">
										      		<p><span class="chAvatarHintTitle"><span class="material-icons-outlined" >warning_amber</span>Ви впевнені ?</span></p>
										      	</div>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
										        <form method="POST" action="../settings/index.php">
										        <input type="submit" name="avatarRemove" class="btn btn-dark" value="Видалити фото">
										        </form>

										      </div>
										    </div>
										  </div>
										</div>
									</div>

								</div>
								
								<div class="userData">
									
									<div class="input-group mb-2">
									  <span class="input-group-text">Ім'я</span>
									  <p class="form-control"><?php echo $_SESSION['first_name'] ?></p>
									</div>
									<div class="input-group mb-2">
									  <span class="input-group-text">Прізвище</span>
									  <p class="form-control"><?php echo $_SESSION['last_name'] ?></p>
									</div>
									<div class="input-group mb-2">
									  <span class="input-group-text">Логін</span>
									  <p class="form-control"><?php echo $_SESSION['login'] ?></p>
									</div>
									<div class="input-group mb-2">
									  <span class="input-group-text">Пошта</span>
									  <p class="form-control"><?php echo $_SESSION['email'] ?></p>
									</div>
									<button  class="btn btn-dark userDataButton"  data-bs-toggle="modal" data-bs-target="#changeData">Змінити дані</button>
									

									<!-- Modal -->
<div class="modal fade " id="changeData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">edit</span>Зміна даних</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="../settings/index.php">
        <div class="input-group mb-2">
									  <span class="input-group-text">Ім'я</span>
									  <input type="text" aria-label="First name" name='first_name' value="<?php echo $_SESSION['first_name'] ?>" class="form-control">
									  <span class="input-group-text">Прізвище</span>
  									  <input type="text" aria-label="Last name" name='last_name' value="<?php echo $_SESSION['last_name'] ?>" class="form-control">
									  
									</div>
									
									<div class="input-group mb-2">
									  <span class="input-group-text">Логін</span>
									  <input type="text" aria-label="Login" name='login' value="<?php echo $_SESSION['login'] ?>" class="form-control">
									</div>
									<div class="input-group mb-2">
									  <span class="input-group-text">Пошта</span>
									  <input type="email" aria-label="Email" name='email' value="<?php echo $_SESSION['email'] ?>" class="form-control">
									</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
        <input type="submit" name="updateUserData" class="btn btn-dark" value="Зберегти">
        </form>

      </div>
    </div>
  </div>
</div>

						</div>

								</div>
								
							</div>


						<div class="settingSubContent">
							<h5 id='aSEditPass'><span class="material-icons">key</span>Зміна паролю
							</h5>
							<?php if (isset($_POST['updateUserPassword']) && $oldpassVerify) {
							?>
							<div class="alert alert-success settingsAccountAlert sAASuccess" role="alert">
							  <span class="material-icons">done</span>
							  <span>Пароль успішно змінено!</span>
							</div>
							<?php } if (isset($_POST['updateUserPassword']) && !$oldpassVerify) {
							?>
							<div class="alert alert-success settingsAccountAlert sAAFail" role="alert">
							  <span class="material-icons">close</span>
							  <span>Старий пароль введно невірно!</span>
							</div>
							<?php }  ?>

							<button  class="btn btn-dark userDataButton" data-bs-toggle="modal" data-bs-target="#changePassword">Змінити пароль</button>

									<!-- Modal -->
<div class="modal fade " id="changePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">key</span>Зміна паролю</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" action="../settings/index.php">
        <div class="input-group mb-2">
									  <span class="input-group-text"><span style="color:black" class="material-icons-outlined" >lock_clock</span></span>
									  <input type="password" name="oldpass" placeholder="Старий пароль" id="oldPassInput" aria-label="First name" class="form-control">
									  <button type="button" onclick="showPass('oldPassInput')" class="input-group-text" ><span style="color:black" class="material-icons" >visibility</span></button>
									</div>
									
									<div class="input-group mb-2">
									  <span class="input-group-text"><span style="color:black" class="material-icons" >key</span></span>
									  <input type="password" name="newpass" placeholder="Новий пароль" id="newPassInput" aria-label="Login" class="form-control">
									  <button type="button" onclick="showPass('newPassInput')"  class="input-group-text" ><span style="color:black" class="material-icons" >visibility</span></button>
									  <script type="text/javascript">
									  	function showPass(passInputId) {
									  		var passInput = document.getElementById(passInputId);
									  		if (passInput.type == 'password') {
									  			passInput.type = 'text';
									  		} else {
									  			passInput.type = 'password';
									  		}
									  	}
									  </script>
									</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
        <input type="submit" name="updateUserPassword" class="btn btn-dark" value="Зберегти">
        </form>

      </div>
    </div>
  </div>
</div>

						</div>
						<div class="settingSubContent">
							<h5 id='aSDelete'>
								<span class="material-icons">delete_forever</span>Видалення акаунту
							</h5>

							<button  class="btn btn-dark userDataButton" data-bs-toggle="modal" data-bs-target="#deleteAccount">Видалити акаунт</button>

									<!-- Modal -->
<div class="modal fade " id="deleteAccount" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-m">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel"><span class="material-icons">delete_forever</span>Видалення акауну</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<p>Ви впевнені що хочете видалити <b>свій акаунт</b> ? Всі дані буде видалено. Ця дія незворотня</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Назад</button>
        <form method="POST" action="../settings/index.php">
        <input type="submit" name="updateUserPassword" class="btn btn-dark" value="Так, видалити">
        </form>

      </div>
    </div>
  </div>
</div>

						</div>
					</div>

				</div>
			</div>


		</div>
	</content>
	<div id='content' ></div>
	<script type="text/javascript">
		$(document).ready(function(){

		    $("#uploadAvatarForm").change(function(){

		        var fd = new FormData();
		        var files = $('#uploadTest')[0].files;
		        
		        // Check file selected or not
		        if(files.length > 0 ){
		           fd.append('file',files[0]);

		           $.ajax({
		              url: '../data/libs/uploadAvatar.php',
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
	
</body>
</html>