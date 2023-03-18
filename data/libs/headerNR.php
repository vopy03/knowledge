<?php
if (isset($_POST['enterf'])) { // Перевіряє, чи натиснута кнопка "Увійти"
$_SESSION['loginError'] = false;
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_STRING);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
$result = $pdo->prepare("SELECT password FROM users WHERE login = :login");
$params = [
"login" => $login,
]; 
$result->execute($params);
$result = $result->fetchAll();
@$pp = $result[0]['password'];
$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
$params = [
"login" => $login,
]; 
$result->execute($params); 
$row_count =$result->fetchColumn();

if ($row_count == false && isset($_POST['enterf'])) $_SESSION['loginError'] = true;
else {
if (password_verify($password, $pp)) {
	$_SESSION['login'] = $login;
	$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
	$params = [
	"login" => $_SESSION['login'],
	]; 
	$result->execute($params); 
	$user = $result->fetchAll();
	$_SESSION = $user[0];
	$_SESSION['full_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
	header('Location: http:index.php');
} else $_SESSION['loginError'] = true; } }
	updUserData($pdo);

 ?>
<header id="mainHeader">
		<a href="../main"><p id="logo">K<b>now</b>ledge.</p></a>
		<!--
		
		-->
		<div id="headerRightContainer" align="right">
		<?php if (!isset($_SESSION['login'])) {	?>
			<div id="HRCNLButt">
			
		<a id="openDialog" class="mainButtons"><span class="material-icons-outlined" id="HRCIcon">login</span><span id="HRCLogin"><?php echo $HRCLogin; ?></span></p></a>
    		<dialog id='loginDialog' class="dialog" open <?php if (@$_SESSION['loginError'] == true) { ?> style="opacity: 1" <?php } else {  ?> hidden <?php } ?>>
				<h4 align="center"><?php echo $HRCSignInTitle; ?></h4>
				<hr width="95%" style="margin-bottom: 5px;">
				<?php

	if (isset($row_count) && isset($_POST['enterf'])) {
		if($row_count == 0) {
			echo "<p align='center'>$HRCSignInLoginError</p>";
		}
		if ($row_count != 0 && ! password_verify($password, $pp) != 0) {
			echo "<p align='center'>$HRCSignInPassError</p>";
		}
	}
	?>
				<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
   <div class="divInputTitle"><span><?php echo $HRCSignInLogin; ?></span><br><input class="intext" type="text" name="login" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-]{3,100}$" title="<?php echo $HRCSignInLoginInputTitle; ?>" autocomplete="off" value="<?php if (isset($login)) echo $login ?>"  placeholder="<?php echo $HRCSignInLoginPlaceholder; ?>" required></div>

   <div class="divInputTitle"><span><?php echo $HRCSignInPass; ?></span><br><input class="intext" type="password" name="password" placeholder="<?php echo $HRCSignInPassPlaceholder; ?>" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-]{8,}$" title="<?php echo $HRCSignInPassInputTitle; ?>" required></div>
   	<hr>
   <button name="enterf" class="btn btn-dark loginButton"><?php echo $HRCSignInButton; ?></button>
   </form>
			
			</dialog> 
		<script src="../data/js/headermenu.js"></script>
		<a href="../reg" class="mainButtons"><p><span class="material-icons" id="HRCIcon">person</span><span id="HRCRegister" ><?php echo $HRCRegiser; ?></span></p></a>
		<div id="headerRightContainerNRThemeToggle" align="right">
		
		<span class="material-icons-outlined" id="HRCDarkMode">dark_mode</span>

		<script type="text/javascript">
			var HRCDarkMode = document.getElementById('HRCDarkMode');
			if(document.body.classList.contains('dark_mode')) {
					HRCDarkMode.innerHTML = 'brightness_5';
				} else {
					HRCDarkMode.innerHTML = 'dark_mode';
				}
			HRCDarkMode.onclick = function () {
				document.body.classList.toggle('dark_mode');
				if(document.body.classList.contains('dark_mode')) {
					HRCDarkMode.innerHTML = 'brightness_5';
				} else {
					HRCDarkMode.innerHTML = 'dark_mode';
				}
			}
			$(document).ready(function(){
				$('#HRCDarkMode').click(function(){
					$.ajax({
						url: "../data/libs/changeTheme.php",
						success: function(html){
							$("#content").html(html);
						}
					});
					return false;
				});
		});
		</script>
	</div>	 
		<?php } else { ?>
		<div class="currentProfileContainer">
			<div class="currentProfileContainerDiv">
			<label for="currentProfileCheckbox" class="currentProfile">
			<img width="50" height="50" id="avatar" src="<?php echo $_SESSION['avatar'] ?>">
			 <span class="currentProfileFirstName"><?php echo $_SESSION['first_name'] ?></span> <span class="currentProfileLastName"><?php echo $_SESSION['last_name'] ?></span>
			</label>
			<input id="currentProfileCheckbox" type="checkbox" name="check" hidden>
			<span class="material-icons expand_more">expand_more</span>
			<div class="currentProfileContextMenu">
				<ul class="currentProfileContextMenuItems">
					<a href="../courses"><li><span class="material-icons-outlined" id="myCoursesSpan">school</span><?php echo $NLMyCourses ; ?></li></a>
					<a href="../tests"><li><span class="material-icons-outlined" id="myTestsSpan">note_alt</span><?php echo $NLTests; ?></li></a>
					<li id="key"><span class="material-icons" id="darkModeSpan">dark_mode</span><?php echo $HRCContextMenuDarkMode; ?><label class="switch"><input id="keych" type="checkbox" <?php if (@$_SESSION['dark'] == 1) { ?> checked <?php } ?> ><span class="slider round"></span></label></li>
					

					<a href="../settings"><li><span id="settingsSpan" class="material-icons">settings</span><?php echo $HRCContextMenuSettings; ?></li></a>
				</ul> 
			<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
			<input type="submit" name="logout" class="btn btn-danger" id="logout" value="Вийти">
			</form>
			</div>
			</div>
		</div>
	</div>
		<?php } ?>

		</div>

</header>
<?php if(isLogged()) { ?>
<script src="../data/js/dbthemetoggler.js"></script>

<script type="text/javascript">
		const e = document.querySelector('#key');
		const el = document.querySelector('#keych');
		e.addEventListener('click', function() {
		el.checked = (el.checked != true) ? true : false;
  		document.body.classList.toggle('dark_mode');
		});		
		el.addEventListener('click', function() {
  		document.body.classList.toggle('dark_mode');
		});
		const contextMenu = document.querySelector('.currentProfileContextMenu')
	var timer = setTimeout((function(val){return function(){contextMenu.style.animationDuration = val};})('0.2s'), 200);
</script>
<?php } ?>