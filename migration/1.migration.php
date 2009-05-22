<?

	$root = "../";
	require_once $root."references.php";

	set_time_limit(120);

	/* Variables */

	$backupDir = "../../../cgi-bin/data/";
	$usersDir = "../".$PathToUsers;
	$statuses = array();

	/* Functions */

	function InitStatuses() {
	  global $statuses, $backupDir;

	    /* Standard statuses */
		$names = array("Никто", "Новичок", "V.I.P", "V.I.P", "V.I.P", "V.I.P", "V.I.P", "V.I.P", "Старожил", "Хранитель", "Священник", "Бот", "Администратор");
		$rights = array(0, 1, 2, 3, 4, 5, 6, 10, 11, 20, 25, 50, 75);
		$colors = array("#808080", "White", "Yellow", "Yellow", "Yellow", "Yellow", "Yellow", "Yellow", "#7F9537", "Orange", "#7F9537", "#7F9537", "#7F9537");

		echo "<h3>Statuses initialization</h3>";
		for ($i = 0; $i < sizeof($rights); $i++) {
			$r = $rights[$i];
			$status = new Status();
			$status->Rights = $r;
			$status->Title = $names[$i];
			$status->Color = $colors[$i];
			$status->Save();

			$statuses["r".$r] = $status->Id;
		}
	
		/* Individual statuses */
	    $fileContents = fopen ($backupDir."status", "r");
	    $lines = split("[\r\n]", fread($fileContents, 65535));
	    fclose($fileContents);

	    for ($i = 0; $i < sizeof($lines); $i++) {
	    	list($number, $title, $color) = split("\|", $lines[$i]);
			$status = new Status();
			$status->Rights = "";
			$status->Title = $title;
			$status->Color = $color;
			$status->IsSpecial = 1;

			$statuses["s".$number] = $status;
	    }
	
	}

	/* Functions */


	echo "<h2>Migration:</h2>";
	
	echo "<h3>Truncate tables</h3>";
	$q = $db->Query("TRUNCATE TABLE ".User::table);
	$q = $db->Query("TRUNCATE TABLE ".Profile::table);
	$q = $db->Query("TRUNCATE TABLE ".Settings::table);
	$q = $db->Query("TRUNCATE TABLE ".Status::table);
	$q = $db->Query("TRUNCATE TABLE ".Nickname::table);
	$q = $db->Query("TRUNCATE TABLE ".Ignore::table);

	$q = $db->Query("TRUNCATE TABLE ".Room::table);
	$q = $db->Query("TRUNCATE TABLE ".Message::table);
	$q = $db->Query("TRUNCATE TABLE ".Wakeup::table);
	$q = $db->Query("TRUNCATE TABLE ".TreeNode::table);
	$q = $db->Query("TRUNCATE TABLE ".AdminComment::table);

	InitStatuses();

	echo "<h3>Parse users records on file system</h3>";

	$dirHandler = opendir($usersDir);
	$counter = 0;
	echo "<b>Saved users:</b><ol>";
	while (($userFile = readdir($dirHandler)) !== false) {
		if (!is_dir($usersDir.$userFile) && $userFile != "index.usr") {
		    $fileContents = fopen ($usersDir.$userFile, "r");
		    $lines = split("[\r\n]", fread($fileContents, 65535));
		    fclose($fileContents);

		    list($guid,$ext) = split("\.", $userFile);
			list($rights, $ban_reason) = split("\|", $lines[2]);
			$rights = round($rights);

		    /* User */
		    $user = new User();
		    $user->Login = $lines[0];
		    $user->Password = $lines[1];

			    /* Status */
			    $statusNum = $lines[30];
			    if ($statusNum) {
				    $status = $statuses["s".$statusNum];
				    if ($status) {
					    if ($status->IsEmpty()) {
					    	$status->Rights = $rights;
					    	$status->Save();
					    }
						$user->StatusId = $status->Id;
					} else {
						error("Status ".$statusNum." not found!");
					}
				} else {
				    $user->StatusId = $statuses["r".$rights];
				}

			$user->SessionAddress = $lines[12];
			$user->BanReason = $ban_reason;
			$user->Guid = $guid;
			$result = $user->Save();


		    if ($result == "" && !$user->IsEmpty()) {

		    	/* Keep tree information... */
				$UserGuid[$user->Guid] = $user->Id;
				if ($lines[20]) {
					$UserRelatives[$user->Id] = $lines[20];
				}
				/* ... for post-processing  */

			    echo "<li>".$user->Login;

			    /* Profile */
			    $profile = new Profile();
		    	$profile->UserId = $user->Id;
			    list($img, $width, $height) = split(",", $lines[3]);
			    $profile->Photo = $img;
			    $profile->Name = $lines[4];
			    $profile->Gender = $lines[5] ? ($lines[5]==1 ? "m" : "f") : "";
				$profile->Birthday = sprintf("%04d-%02d-%02d", $lines[8], $lines[7], $lines[6]);
				$profile->Email = $lines[9];
				$profile->Icq = substr($lines[10], 0, 20);
				if ($lines[11] && $lines[11] != "http://") {
					$profile->Url = $lines[11];
				}
				$profile->Registered = DateFromTime($lines[13]);
				$profile->LastVisit = DateFromTime($lines[14]);
				$profile->Generation = $lines[21];

				$profile->About = str_replace("<br>", "\n", $lines[28]);

				$profile->Save();

				/* Settings */
			    $settings = new Settings();
			    $settings->UserId = $user->Id;
			    $settings->Status = $lines[22];
			    $settings->EnterMessage = $lines[23];
			    $settings->QuitMessage = $lines[24];
			    list(
			    	$settings->FontColor,
			    	$settings->FontIsBold,
		    		$settings->FontIsItalic, 
		    		$settings->FontIsUnderlined, 
			    	$settings->FontFace, 
			    	$settings->FontSize, 
			    	$settings->Refresh, 
		    		$settings->ReceiveWakeups, 
		    		$settings->ConfirmPrivates
			    	) = split("[\|&]", $lines[17]);

			    $settings->FontSize = round($settings->FontSize);
		    	$settings->FontIsBold = Boolean($settings->FontIsBold);
	    		$settings->FontIsItalic = Boolean($settings->FontIsItalic);
	    		$settings->FontIsUnderlined = Boolean($settings->FontIsUnderlined);

		    	list($ignore_colors, $ignore_sizes, $ignore_styles, $ignore_faces, $disable_links, $frames_configuration) = split("\|", $lines[25]);
				$settings->IgnoreFonts = Boolean($ignore_faces);
				$settings->IgnoreColors = Boolean($ignore_colors);
				$settings->IgnoreSizes = Boolean($ignore_sizes);
				$settings->IgnoreStyles = Boolean($ignore_styles);

		    	$settings->Refresh = round(10 * $settings->Refresh);
		    	if ($settings->Refresh <= 10) {
	    			$settings->Refresh = 20;
	    		}

			    $settings->Save();
			    $settings->Retrieve();

			    /* Nickname */
		    	if ($lines[27]) {
				    $nick = new Nickname();
				    $nick->UserId = $user->Id;
				    $nick->Title = $lines[27];
				    $nick->IsSelected = 1;
				    $nick->Save();
				}

			    $counter++;
			} else {
				error("Error saving user ".$user->Login);
			}
		}
	}
	echo "</ol>".$counter." records found.";

	/* Tree nodes */
	echo "<h3>Saving users relations:</h3>";

	while (list($id, $relations) = each($UserRelatives)) {
		$relationsSplit = split("&", $relations);
//		echo "<ul><b>".$id."</b>";
		for ($i = 0; $i < sizeof($relationsSplit); $i++) {
			list($type, $guid2) = split("=", $relationsSplit[$i]);

			$node = new TreeNode();
			$node->FirstUserId = $UserGuid[$guid2];
			$node->SecondUserId = $id;
			$node->RelationType = substr($type, 1, 1);
			if ($node->IsValid()) {
				$node->Save();
//				echo $node;
			} else {
//				error($node->__tostring());
			}
		}
//		echo "</ul>";
	}

	echo "Migration completed!";


?>