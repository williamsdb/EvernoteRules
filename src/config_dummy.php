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

// Remember The Milk keys - leave blank if not using
define("RTM_API_KEY", "");
define("RTM_API_SECRET", "");
define("RTM_AUTH_TOKEN", "");

// Log level
// 0 - off
// 1 - write errors to log file
// 2 - verbose logging and record webhooks 
define("DEBUG", 0);

// Your oAuth token - do not enter anything here!
define("OAUTH", "");
