<?php
require_once "../dbconnect.php";

if (isset($_POST['regf'])) { // Перевіряє, чи натиснута кнопка "Зареєструватись"	
$login = filter_var( trim($_POST['login']), FILTER_SANITIZE_STRING);
$first_name = filter_var( trim($_POST['first_name']), FILTER_SANITIZE_STRING);
$last_name = filter_var( trim($_POST['last_name']), FILTER_SANITIZE_STRING);
$password = filter_var( trim($_POST['password']), FILTER_SANITIZE_STRING);
$password2 = filter_var( trim($_POST['password2']), FILTER_SANITIZE_STRING);
$email = filter_var( trim($_POST['email']), FILTER_SANITIZE_STRING);
if ($password == $password2) {
	$er = false;
	$result = $pdo->prepare("SELECT * FROM users WHERE login = :login ");
	$params = [ "login" => $login,]; 
	$result->execute($params); 
	$row_count =$result->fetchColumn();
if($row_count == 0) {
	$sql = "INSERT INTO `users` (`login`, `first_name`, `last_name`, `email`, `password`) VALUES (:login, :first_name, :last_name, :email, :password)";
	$result = $pdo->prepare($sql);
	$params = ["login" => $login, "first_name" => $first_name, "last_name" => $last_name, "email" => $email, "password" => password_hash($password, PASSWORD_DEFAULT), ]; 
	$result->execute($params); } } else $er = true; }
	include '../data/libs/includes.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $regTitle; ?></title>
</head>
<body>

	<header id="mainHeaderReg">
	<a href="../main"><p id="logoReg">K<b>now</b>ledge.</p></a>
	<div id="headerRightContainerReg" align="right">
		
		<span class="material-icons-outlined" id="HRCDarkMode">dark_mode</span>

		<script type="text/javascript">
			var HRCDarkMode = document.getElementById('HRCDarkMode');
			HRCDarkMode.onclick = function () {
				document.body.classList.toggle('dark_mode');
				if(HRCDarkMode.innerHTML == 'dark_mode') {
					HRCDarkMode.innerHTML = 'brightness_5';
				} else {
					HRCDarkMode.innerHTML = 'dark_mode';
				}
			}
		</script>
	</div>
	</header>
	<div class="area" >
            <ul class="circles">
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
            </ul>
    </div>
	<div class="regpage">
	<h2>Реєстрація</h2>
	<hr>
	<?php

if (isset($_POST['regf'])) {
		if ($er == true) { ?>
			<div class="alert alert-warning"><p><span class="chAvatarHintTitle" style="color: black;"><span class="material-icons-outlined" style="color: black;" >warning_amber</span>Паролі не співпадають!</span></p></div>

		<?php } else { 
			if($row_count != 0){ ?>
			<div class="alert alert-warning"><p><span class="chAvatarHintTitle" style="color: black;"><span class="material-icons-outlined" style="color: black;" >warning_amber</span>Користувач під таким логіном вже зареєстрований!</span></p></div>
			<?php } 
		else if($row_count == 0) { ?>
				<div class="alert alert-dark sAASuccess"><p><span class="chAvatarHintTitle" style="color: black;"><span class="material-icons-outlined" style="color: black;" >info</span>Ви успішно зареєструвались. Тепер ви можете авторизуватись.</span></p></div>
    		<p align='center'></p>
<?php
		}
	}	
	}

?>
  <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
  	<div class="userDetails" >
   <div class="divInputTitle"><span class="inputTitle">Логін </span><br><input class="intext" type="text" name="login" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-]{3,100}$" title="Назва повинна містити від 3 до 100 символів" autocomplete="off"  placeholder="Ваш логін" value="<?php if (isset($login)) echo $login ?>" required></div>
   <div class="divInputTitle"><span class="inputTitle">Пошта </span><br><input class="intext" type="email" name="email" autocomplete="off" placeholder="Ваш E-mail" value="<?php if(isset($email)) echo $email ?>" required></div>
   <div class="divInputTitle"><span class="inputTitle">Ім'я </span><br><input class="intext" type="text" name="first_name" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-і]{3,100}$" title="Назва повинна містити від 3 до 100 символів" autocomplete="off"  placeholder="Ваше ім'я" value="<?php if (isset($first_name)) echo $first_name ?>" required></div>
   
   <div class="divInputTitle"><span class="inputTitle">Прізвище </span><br><input class="intext" type="text" name="last_name" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-і]{3,100}$" title="Назва повинна містити від 3 до 100 символів" autocomplete="off"  placeholder="Ваше прізвище" value="<?php if (isset($last_name)) echo $last_name ?>" required></div>
   <div class="divInputTitle"><span class="inputTitle">Пароль </span><br><input class="intext" autocomplete type="password" name="password" placeholder="Ваш пароль" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-і]{8,}$" title="Пароль повинен містити мінімум 8 символів" required></div>
   <div class="divInputTitle"><span class="inputTitle">Повторіть пароль </span><br><input autocomplete class="intext" type="password" name="password2" placeholder="Ваш пароль" pattern="^[A-Za-zА-Яа-я0-9,\.\(\)\-і]{8,}$" title="Пароль повинен містити мінімум 8 символів" required></div>
   </div>
   	<hr>
   <button name="regf" class="btn btn-dark regButton">Зареєструватись</button>
   </form>
	</div>
</body>
</html>