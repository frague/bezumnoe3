<?php

    $someoneIsLogged = 0;

    $user = GetAuthorizedUser(true);

    if (!$user->IsEmpty()) {
        $user->User->TouchSession();
        $user->User->Save();
        $someoneIsLogged = 1;
    }

?>
