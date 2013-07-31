<?php

	require "../inc/ui_parts/templates.php";
	require "../inc/base_template.php";

	function IsPostingAllowed() {
		return !AddressIsBanned(new Bans(0, 0, 1));
	}

	// Gets Journal Template by given Settings object
	// Dies with 404 if not found
	function GetTemplateOrDie($settings) {
		$template = new JournalTemplate();
		if ($settings->SkinTemplateId > 0) {
			// Getting template from skin in settings if set
			$template->GetById($settings->SkinTemplateId);
		} else {
			if ($settings->OwnMarkupAllowed) {
				// if custom template permitted, read it
				$template->FillByForumId($settings->ForumId);
			}

			if ($template->IsEmpty()) {
				// Getting default template
				$skin = new JournalSkin();
				$template->GetById($skin->GetDefaultTemplateId());
			} elseif (!$template->IsComplete()) {
				// Merging empty template fields from Default
				$skin = new JournalSkin();
				$defaultTemplate = new JournalTemplate();
				$defaultTemplate->GetById($skin->GetDefaultTemplateId());

				if (!$defaultTemplate->IsEmpty()) {
					$template->MergeWith($defaultTemplate);
					if ($template->Id != $defaultTemplate->Id) {
						$template->Save();
					}
				}
			}
		}
	
		if (!$template) {
			DieWith404();
		}
		return $template;
   	}


?>
