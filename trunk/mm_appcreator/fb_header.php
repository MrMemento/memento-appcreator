<?

if ($member == true)
	print <<<HTML
<fb:if-user-has-added-app>
	<fb:dashboard>
		<fb:create-button href="http://apps.facebook.com/mm_appcreator/create_application.php">Create new Application</fb:create-button>
		<fb:action href="http://apps.facebook.com/mm_appcreator/">Dashboard Home</fb:action>
		<fb:action href="http://apps.facebook.com/mm_appcreator/index.php?action=logout">Logout</fb:action>
		<fb:help href="user_profile.php">$directory</fb:help>
	</fb:dashboard>
	<fb:else>
		<fb:dashboard>
			<fb:create-button href="http://apps.facebook.com/mm_appcreator/create_application.php">Create new Application</fb:create-button>
			<fb:action href="http://apps.facebook.com/mm_appcreator/">Dashboard Home</fb:action>
			<fb:action href="http://apps.facebook.com/mm_appcreator/index.php?action=logout">Logout</fb:action>
			<fb:action href="http://apps.facebook.com/add.php?api_key=">Add this app</fb:action>
			<fb:help href="user_profile.php">$directory</fb:help>
		</fb:dashboard>
	</fb:else>
</fb:if-user-has-added-app>
HTML;
else
	print <<<HTML
<fb:if-user-has-added-app>
	<fb:dashboard>
	</fb:dashboard>
	<fb:else>
		<fb:dashboard>
			<fb:action href="http://apps.facebook.com/add.php?api_key=">Add this app</fb:action>
		</fb:dashboard>
	</fb:else>
</fb:if-user-has-added-app>

HTML;

?>