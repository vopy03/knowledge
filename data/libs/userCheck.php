<?php
	if (isset($_POST['logout'])) {
		unset($_SESSION['login']);
		session_unset();
	}
	function isLogged() {
		if (isset($_SESSION['login'])) return true;
		else return false;
	}
	function userCheck() { if (!isset($_SESSION['login'])) header('Location: ../index.php'); }

	function updUserData($pdo) {
		if (isset($_SESSION['login'])) {
	$result = $pdo->prepare("SELECT * FROM users WHERE login = :login");
			$params = [
			"login" => $_SESSION['login'],
			]; 
			$result->execute($params); 
			$user = $result->fetchAll();
			$_SESSION['id'] = $user[0]['id'];
			$_SESSION['first_name'] = $user[0]['first_name'];
			$_SESSION['last_name'] = $user[0]['last_name'];
			$_SESSION['avatar'] = $user[0]['avatar'];
			$_SESSION['email'] = $user[0]['email'];
			$_SESSION['full_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
			$_SESSION['dark'] = $user[0]['dark'];
}

}
	function changeDark($pdo, $login, $dark) {
		$_SESSION['dark_old'] = $dark;
			$sql = "UPDATE `users` SET `dark` = :dark WHERE `login` = :login";
			$params = [
			"dark" => $dark,
		    "login" => $login,
			];
			$prepare = $pdo -> prepare($sql);
			$prepare -> execute($params);
		}
		if(isset($_SESSION['login']) && @$_SESSION['dark_old'] != $_SESSION['dark'] ) { changeDark($pdo, $_SESSION['login'], $_SESSION['dark']);}
 ?>