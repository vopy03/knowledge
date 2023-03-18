<?php
require_once "../../dbconnect.php";
    //var_dump($_POST);
	
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

	$result = $pdo->prepare("SELECT tm_id FROM test_members WHERE test_id = :test_id AND user_id = :user_id ");
    $params = [
    "test_id" => $_GET['testId'],
    "user_id" => $_SESSION['id'],
    ];
    $result->execute($params); 
    $usr_mem = $result->fetchAll();
    



	


    $result = $pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
        $result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
        $result->execute(); 
        $test = $result->fetchAll();
        $test = $test[0];

    $result = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :creator_id");
        $result->bindParam(':creator_id', $test['creator_id'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test_creator = $result->fetchAll();
        $current_test_creator = $current_test_creator[0][0] . ' ' . $current_test_creator[0][1];

include '../../data/libs/includesDeep.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Результати тесту: <?php echo $test['name'];?> - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>

    <?php
        if ($_SESSION['id'] == $test['creator_id']) {
            include 'tVR.creator.php';
        } else {
            include 'tVR.user.php';
}


     ?>
    </body>
</html>