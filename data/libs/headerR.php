<header id="mainHeader">
		<a href="../main"><p id="logo">K<b>now</b>ledge.</p></a>
		<!--
		
		-->
		<div id="headerRightContainer" align="right">
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

</header>
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