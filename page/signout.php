<?php

if($_host_session->is_login){
    $_host_session->logout();
}
redirect_uri(_URL);
