<?php

    // Callback URL for OAuth (no trailing slash)
    define("CALLBACK_URL", "https://yourdomin.com");

    // Your Evernote api keys
    define("KEY", "<your api comsumer key>");
    define("SECRET", "<your api consumer secret>");

    // your Evernote user id - leave blank if you don't know it
    define("USER", "");

    // Pushover keys - leave blank if not using
    define("PUSHOVER_TOKEN", "");
    define("PUSHOVER_USER", "");    

    // Log level
    // 0 - off
    // 1 - write to log file
    // 2 - write to log file and record webhooks 
    define("DEBUG", 0);

    // Your oAuth token - do not enter anything here!
    define("OAUTH","");

?>