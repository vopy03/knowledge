<?php 

if (isset($_POST['addAttempt'])) {
	echo 'add';

	$result = $pdo->prepare("SELECT remaining_attempts FROM `test_members` WHERE `tm_id` = :tm_id AND test_id = :test_id");
    $params = [
    "tm_id" => $_POST['mem_id'],
    "test_id" => $_GET['testId'],
    ]; 
    $result->execute($params); 
    $member = $result->fetchAll();
    $memberRA = $member[0][0];


	$sql = "UPDATE `test_members` SET `remaining_attempts`= :remaining_attempts WHERE test_id = :test_id AND tm_id = :tm_id ";

    $result = $pdo->prepare($sql);
    $params = [
    "test_id" => $_GET['testId'],
    "tm_id" => $_POST['mem_id'],
    "remaining_attempts" => $memberRA+1,
    ]; 
    $result->execute($params);

    header('Location: ../../tests/vr/'.$_GET['testId']);

}


if (isset($_POST['remAttempt'])) {
	echo 'rem';
	$result = $pdo->prepare("SELECT remaining_attempts FROM `test_members` WHERE `tm_id` = :tm_id AND test_id = :test_id");
    $params = [
    "tm_id" => $_POST['mem_id'],
    "test_id" => $_GET['testId'],
    ]; 
    $result->execute($params); 
    $member = $result->fetchAll();
    $memberRA = $member[0][0];


	$sql = "UPDATE `test_members` SET `remaining_attempts`= :remaining_attempts WHERE test_id = :test_id AND tm_id = :tm_id ";

    $result = $pdo->prepare($sql);
    $params = [
    "test_id" => $_GET['testId'],
    "tm_id" => $_POST['mem_id'],
    "remaining_attempts" => $memberRA-1,
    ]; 
    $result->execute($params);

    header('Location: ../../tests/vr/'.$_GET['testId']);
}


?>

    


		

				

			<?php if(!isset($_GET['secondId'])) {

        $fp = fopen("../".$test['filepath'], "r"); // Открываем файл в режиме чтения
        if ($fp) {
            $q_number = 0;
            $config_part = false;
            while (!feof($fp)) {

                $str = fgets($fp);  
                if (str_contains($str, 'econfig')) {
                    fclose($fp);
                    break;
                }
                if ($config_part) {
                    $config_name = substr($str , 0 ,strpos($str, '=')-1);
                    $config_value = substr($str , strpos($str, '=')+2);
                    $test["$config_name"] = trim($config_value);
                }

                if (str_contains($str, 'sconfig')) {
                    $config_part = true;
                }

            }

        } else echo "Ошибка при открытии файла";


        	$result = $pdo->prepare("SELECT tests.id, name, member_name, user_id, tm_id, num_of_questions, remaining_attempts FROM tests, test_members WHERE test_id = :test_id AND test_id = tests.id");
		    $params = [
		    "test_id" => $_GET['testId'],
		    ];
		    $result->execute($params); 
		    $test_results = $result->fetchAll();



include '../../data/libs/headerRDeep.php'; 
                ?>
                <content>
		<div class="content testAllContent VRtestAC" >
			<div class="testHeader">
                <h3><span class="material-icons-outlined" >assignment</span><?php echo $test['filename']; ?></h3>
                <div style="position: absolute; right: 2%; top:20%; background-color: var(--test-info-color); padding: 10px; border-radius: 5px;">
            	<a href="../s/<?php echo $test['id']; ?>">
			      	<div class="testsMyTestsAction rounded" title="Перейти до тесту">
			      		<span class="material-icons-outlined" >edit_calendar</span>
			      	</div>
		      	</a>
		      	<a href="<?php echo $test['filepath']; ?>" download>
		      		<div class="testsMyTestsAction rounded" title="Завантажити">
		      			<span class="material-icons" >download</span>
	  				</div>
	  			</a>
		      	<a href="../index?testDeleteId=<?php echo $test['id']; ?>"><div class="testsMyTestsAction rounded" title="Видалити">
		      		<span class="material-icons-outlined" >delete</span>
		      	</div></a>
	  			</div>

            </div>
            <div class="testContent" >
            	<h4><span class="material-icons-outlined" >assignment</span>Всі результати</h4>
            	<div class="testsMyTestsList">
            	<table class="table table-borderless table-striped table-hover">
						  <thead>
						    <tr>
						    	<th></th>
						      <th scope="col">Користувач</th>
						      <th scope="col">Спроб залишилось</th>
						      <th scope="col">Результатів</th>
						      <th scope="col">Дії</th>
						    </tr>
						  </thead>
						  <tbody>
						  	<?php foreach ($test_results as $key => $t) {


						  		$result = $pdo->prepare("SELECT tm_id FROM test_members WHERE tm_id = :user_id AND test_id = :test_id");
								$result->bindParam(':user_id', $t['tm_id'], PDO::PARAM_INT);
								$result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
								$result->execute(); 
								$mem = $result->fetchAll();
								$mem = $mem[0];

								$result = $pdo->prepare("SELECT COUNT(*) FROM test_results WHERE test_member_id = :test_member_id ORDER BY `test_results`.`completion_datetime` DESC");
								$result->bindParam(':test_member_id', $mem['tm_id'], PDO::PARAM_INT);
								$result->execute();
								$res = $result->fetchAll();
						  	 ?>
						    	<tr>

						    	<th><span class="material-icons-outlined" >article</span></th>
						      <td scope="row">
						      	<p style="display:flex; justify-content:center; gap:3px"><?php 
						      	if(!isset($t['user_id'])) { ?>
						      		<span class="material-icons-outlined" title="Цей користувач не зареєстрований" style="font-size:16px" >no_accounts</span>
						      	<?php }
						      	echo $t['member_name']; ?></p>
						      </td>
						      <td><?php echo $t['remaining_attempts']; ?></td>
						      <td><?php echo $res[0][0]; ?></td>
						      <td>
						      	<a href="<?php echo $t['id'].'-'.$t['tm_id']; ?>">
						      	<div class="testsMyTestsAction rounded" title="Переглянути результати">
		      						<span class="material-icons-outlined" >visibility</span>
		      					</div>
		      					</a>
		      					<div class="testsMyTestsAction rounded" title="Додати спробу">
		      						<form method="POST" action="<?php $_GET['testId'] ?>">
		      							<input type="submit" id="aAtpt<?php echo $t['tm_id']; ?>" name="addAttempt" hidden>
		      							<input type="number" name="mem_id" value="<?php echo $t['tm_id']; ?>" hidden>
		      							<label for="aAtpt<?php echo $t['tm_id']; ?>"><span class="material-icons-outlined" >add</span></label>
		      						</form>
		      					</div>
		      					<div class="testsMyTestsAction rounded" title="Відняти спробу">
		      						<form method="POST" action="<?php $_GET['testId'] ?>">
		      							<input type="submit" id="rAtpt<?php echo $t['tm_id']; ?>" name="remAttempt" hidden>
		      							<input type="number" name="mem_id" value="<?php echo $t['tm_id']; ?>" hidden>
		      							<label for="rAtpt<?php echo $t['tm_id']; ?>"><span class="material-icons-outlined" >remove</span></label>
		      						</form>
		      						
		      					</div>
		      					
		      				</td>
						    </tr>
						  </tbody>
						<?php }?>
						</table>
					</div>
					</div>
                <?php

echo "<a href='../../main/' class='btn btn-dark testSubmit'>На головну</a>";
} else {
	include 'tVR.user.php';
}
?>


	