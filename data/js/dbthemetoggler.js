$(document).ready(function(){
		
			$('#key').click(function(){
				$.ajax({
					url: "../data/libs/changeTheme.php",
					success: function(html){
						$("#content").html(html);
					}
				});
				return false;
			});
		});
