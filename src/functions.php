<?php

function pushover($message, $token, $user)
{

    // only bother if Pushover details set in config
    if (empty($token) || empty($user)) die;

    // Send to PushOver
    curl_setopt_array($ch = curl_init(), array(
        CURLOPT_URL => "https://api.pushover.net/1/messages.json",
        CURLOPT_POSTFIELDS => array(
            "token" => $token,
            "user" => $user,
            "message" => $message,
        ),
        CURLOPT_RETURNTRANSFER => true,
    ));
    curl_exec($ch);
    unset($ch);

    return;
}

function write_oauth_key($oauth, $file)
{

    // Read the file contents
    $file_contents = file_get_contents($file);

    // Regular expression to find the text within the double quotes
    $pattern = '/define\("OAUTH","[^"]*"\);/';

    // Replacement string
    $replacement = 'define("OAUTH","' . $oauth . '");';

    // Replace the text
    $new_contents = preg_replace($pattern, $replacement, $file_contents);

    // Write the new contents back to the file
    file_put_contents($file, $new_contents);

    return;
}

function getNotebooks($smarty, $webhook = TRUE)
{

    $client = new \Evernote\Client(OAUTH, FALSE, null, null, FALSE);

    $notebooks = array();
    try {
        // Process the list of notebooks
        $notebooks = $client->listNotebooks();

        // Check if $notebooks is empty
        if (empty($notebooks)) {
            if ($webhook) {
                debug('getNotebooks - No notebooks found.', 1, 'getNotebooks', 10);
            } else {
                $smarty->assign('error', 'No notebooks found in your account.');
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
            }
            die;
        }
    } catch (EDAMUserException $e) {
        if ($e->getCode() === EDAMErrorCode::AUTH_EXPIRED || $e->getCode() === 9) {
            if ($webhook) {
                debug('getNotebooks - Token has expired.', 1, 'getNotebooks', 20);
            } else {
                $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
            }
            die;
        } else {
            if ($webhook) {
                debug('getNotebooks - An error occurred: ' . $e->getMessage(), 1, 'getNotebooks', 30);
            } else {
                $smarty->assign('error', 'An error occurred: ' . $e->getMessage());
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
            }
            die;
        }
    } catch (EDAMSystemException $e) {
        if ($webhook) {
            debug('getNotebooks - System error: ' . $e->getMessage(), 1, 'getNotebooks', 40);
        } else {
            $smarty->assign('error', 'System error: ' . $e->getMessage());
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
        }
        die;
    } catch (EDAMNotFoundException $e) {
        if ($webhook) {
            debug('getNotebooks - Requested resource not found: ' . $e->getMessage(), 1, 'getNotebooks', 50);
        } else {
            $smarty->assign('error', 'Requested resource not found: ' . $e->getMessage());
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
        }
        die;
    } catch (Exception $e) {
        // Catch any other unexpected exceptions
        if ($webhook) {
            debug('getNotebooks - Unexpected error: ' . $e->getMessage(), 1, 'getNotebooks', 60);
        } else {
            $smarty->assign('error', 'Unexpected error: ' . $e->getMessage());
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
        }
        die;
    }

    // format the returned list of notebooks
    foreach ($notebooks as $notebook) {
        $result[] = array("guid" => $notebook->guid, "name" => $notebook->name);
    }

    usort($result, 'compareByName');
    return $result;
}

function getTags($client)
{

    $tags = array();
    try {

        // Process the list of tags
        $tags = $client->listTags();
    } catch (EDAMUserException $e) {

        if ($e->getCode() === EDAMErrorCode::AUTH_EXPIRED || $e->getCode() === 9) {
            if ($webhook) {
                debug('Token has expired. getTags 1', 1, 'getTags', 10);
            } else {
                $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
            }
            die;
        } else {
            // Handle other exceptions
            if ($webhook) {
                debug('An error occurred: ' . $e->getMessage(), 1, 'getTags', 20);
            } else {
                $smarty->assign('error', 'An error occurred: ' . $e->getMessage());
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
            }
            die;
        }
    }

    if (empty($tags)) {
        if ($webhook) {
            debug('No tags found.', 1, 'getTags', 30);
        } else {
            $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
        }
        die;
    } else {
        foreach ($tags as $tag) {
            $result[] = array("guid" => $tag->guid, "name" => $tag->name);
        }
    }

    usort($result, 'compareByName');
    return $result;
}

function compareByName($a, $b)
{
    return strcmp($a['name'], $b['name']);
}

function findNameByGuid($array, $guid)
{
    foreach ($array as $element) {
        if ($element['guid'] === $guid) {
            return $element['name'];
        }
    }
    // Return null or an appropriate value if the guid is not found
    return null;
}

function findGuidByName($array, $name)
{
    foreach ($array as $element) {
        if ($element['name'] === $name) {
            return $element['guid'];
        }
    }
    // Return null or an appropriate value if the guid is not found
    return null;
}

function startsWith($haystack, $needle)
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function readRules()
{
    // Read the rules database
    try {
        $rules = file_get_contents('./rules.db');
    } catch (\Throwable $th) {
        die('rules.db file not found. Have you created it?');
    }
    return unserialize($rules);
}

function writeRules($rules)
{
    // write the rules to the database file
    try {
        file_put_contents('./rules.db', serialize($rules));
    } catch (\Throwable $th) {
        die('rules.db file not found. Have you created it?');
    }
}

function checkTitleCondition($title,  $condition, $conditionText)
{

    // does the title given meet the condition?
    if ($condition == '0') {
        return TRUE;
    }
    if ($condition == '1' && $title == $conditionText) {
        return TRUE;
    }
    if ($condition == '2' && str_contains($title, $conditionText)) {
        return TRUE;
    }
    if ($condition == '3' && startsWith($title, $conditionText) != '') {
        return TRUE;
    }
    if ($condition == '4' && endsWith($title, $conditionText) != '') {
        return TRUE;
    }

    return FALSE;
}

function checkAuthorCondition($author, $authorText)
{

    // does the author text given meet the condition?
    if (str_contains($author, $authorText)) return TRUE;

    return FALSE;
}

function checkTagCondition($tags, $conditionTags)
{

    // we only need to check if there are condition tags and note tags
    if ((empty($tags) && empty($conditionTags)) || (!empty($tags) && empty($conditionTags))) return TRUE;
    if ((empty($tags) && !empty($conditionTags))) return FALSE;

    // turn the comma separated list into an array
    $condTags = explode(',', $conditionTags);

    // walk through the array seeing if these tags exists on the note itself
    $i = 0;
    $state = 0;
    while ($i <= count($condTags)) {
        $j = 0;
        while ($j <= count($tags)) {
            if (trim($condTags[$i]) == $tags[$j]) $state++;
            $j++;
        }
        $i++;
    }

    if ($state == count($condTags)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function processActions($actions, $ruleName, $title, $client, $note, $noteStore, $noteGuid, $errdate)
{

    // debug incoming request
    debug('process - start', 2, 'processActions', 10);

    // get current tags
    $tags = getTags($noteStore);

    // debug incoming request
    debug('process - action ' . count($actions) . ' actions to process', 2, 'processActions', 20);

    // cycle through the actions 
    for ($i = 0; $i < count($actions); $i++) {

        // debug incoming request
        debug('process - action ' . $i . ' ' . $actions[$i]['option'], 2, 'processActions', 30);

        // process the action
        switch ($actions[$i]['option']) {

            // move to notebook
            case 'move':

                // debug incoming request
                debug('process - move', 2, 'processActions', 40);

                try {
                    $notebook = new \Evernote\Model\Notebook();
                    $notebook->guid = $actions[$i]['moveNotebookGuid'];
                    $moved_note = $client->moveNote($note, $notebook);

                    debug("Note moved successfully.", 2, 'processActions', 50);
                } catch (Exception $e) {
                    debug('Error moving note: ' .  $e->getMessage());
                }

                break;

            // change the title
            case 'subject':

                // debug incoming request
                debug('process - old subject=' . $title, 2, 'processActions', 60);

                // Replace the text
                $new_contents = str_replace($actions[$i]['subjectFind'], $actions[$i]['subjectReplace'], $title);

                // debug incoming request
                debug('process - new subject=' . $new_contents, 2, 'processActions', 70);

                try {
                    $ret = $client->getNote($noteGuid);
                    $edamNote = $ret->getEdamNote();
                    $edamNote->title = $new_contents;

                    // Update the note on the server
                    $noteStore = $client->getAdvancedClient()->getNoteStore();
                    $updatedNote = $noteStore->updateNote(OAUTH, $edamNote);

                    debug("Note updated successfully! New title: " . $updatedNote->title, 2, 'processActions', 80);
                } catch (Exception $e) {
                    debug('Error updating note: ' .  $e->getMessage(), 1, 'processActions', 90);
                    debug('process - subject error=' . $e->getMessage(), 2, 'processActions', 100);
                }

                break;

            // add tags
            case 'tags':

                $tagList = explode(',', $actions[$i]['tags']);

                // debug incoming request
                debug('process - tags ' . count($tagList) . ' to process', 2, 'processActions', 110);

                // are there any tags specified?
                if (count($tagList) == 0) break;

                // cycle through the tags to format them
                $tagsToAdd = [];
                $j = 0;
                while ($j < count($tagList)) {

                    // debug incoming request
                    debug('process - tags pre=' . $j . ' ' . $tagList[$j], 2, 'processActions', 120);

                    // remove any spaces
                    $tagList[$j] = trim($tagList[$j]);

                    // are there any variables?
                    $tagList[$j] = parse_content_for_variables($tagList[$j]);

                    // debug incoming request
                    debug('process - tags post=' . $j . ' ' . $tagList[$j], 2, 'processActions', 130);

                    // does the tag already exist, if not create
                    $tag = findGuidByName($tags, $tagList[$j]);

                    // debug incoming request
                    debug('process - tags pre=' . $j . ' ' . $tag, 2, 'processActions', 140);

                    if ($tag == '') {
                        //create tag
                        $newTag = new \EDAM\Types\Tag;
                        $newTag->name = $tagList[$j]; // Set the desired tag name

                        try {
                            $createdTag = $noteStore->createTag(OAUTH, $newTag);
                            debug("Tag created successfully. Tag GUID: " . $createdTag->guid, 2, 'processActions', 150);
                        } catch (\EDAM\Error\EDAMUserException $e) {
                            if ($e->errorCode == \EDAM\Error\EDAMErrorCode::DATA_CONFLICT) {
                                debug("A tag with this name already exists. " . $tagList[$j], 1, 'processActions', 160);
                                debug('process - tags A tag with this name already exists.' . $tagList[$j], 2, 'processActions', 170);
                            } else {
                                debug("An error occurred: " . $e->getMessage(), 1, 'processActions', 180);
                                debug('process - tags An error occurred 1: ' . $e->getMessage(), 2, 'processActions', 190);
                            }
                        } catch (\Exception $e) {
                            debug("An error occurred: " . $e->getMessage(), 1, 'processActions', 200);
                            debug('process - tags An error occurred 2: ' . $e->getMessage(), 2, 'processActions', 210);
                        }
                    }

                    // Build the tag array
                    array_push($tagsToAdd, $tag);

                    $j++;
                }

                // Add any tags to the note and update
                try {

                    // Get the note to be updated
                    $ret = $client->getNote($noteGuid);
                    $edamNote = $ret->getEdamNote();

                    // Merge existing tags with new tags
                    if (!empty($edamNote->tagGuids)) {
                        $edamNote->tagGuids = array_merge($edamNote->tagGuids, $tagsToAdd);
                    } else {
                        $edamNote->tagGuids = $tagsToAdd;
                    }

                    // Remove duplicates by converting to array keys and back
                    $edamNote->tagGuids = array_values(array_unique($edamNote->tagGuids));

                    // Update the note on the server
                    $updatedNote = $noteStore->updateNote(OAUTH, $edamNote);

                    debug("Note updated successfully! New tags: " . $actions[$i]['tags'], 2, 'processActions', 220);
                } catch (Exception $e) {
                    debug('Error updating note: ',  $e->getMessage(), 2, 'processActions', 230);
                    debug('process - tags An error occurred updating note: ' . $e->getMessage(), 2, 'processActions', 240);
                }

                break;

            // send pushover notification
            case 'pushover':

                // debug incoming request
                debug('process - pushover', 2, 'processActions', 250);

                return pushover('Rule ' . $ruleName . ' has just been triggered', PUSHOVER_TOKEN, PUSHOVER_USER);
                break;

            // delete the note
            case 'delete':

                // debug incoming request
                debug('process - delete', 2, 'processActions', 260);

                $client->deleteNote($note);
                break;

            // bad case
            default:
                debug('Action id ' . $actions[$i]['option'] . ' has not been recognised', 1, 'processActions', 270);
                return 'Action id ' . $actions[$i]['option'] . ' has not been recognised';
                break;
        }
    }

    // debug incoming request
    debug('process - action finished', 2, 'processActions', 280);
}

// log calls
function debug($string, $level = 1, $function = '', $pos = 0)
{
    if (DEBUG == 0) return;

    // write the debug string to the log file
    try {
        $dt = DateTime::createFromFormat('U.u', microtime(true));
        $hr = hrtime(true); // nanoseconds (int)

        if (($level <= 1 && DEBUG == 1) || ($level <= 2 && DEBUG == 2)) {
            $timestamp = $dt->format("Ymd_His") . sprintf('.%06d', $dt->format("u"));
            $uniqueId = substr($hr, -6);

            $line = $timestamp . '_' . $uniqueId . ',' . $function . ',' . $pos . ',' . '"' . $string . '"' . PHP_EOL;

            file_put_contents('./logs.db', $line, FILE_APPEND);
        }
    } catch (\Throwable $th) {
        die('logs.db file not found. Have you created it?');
    }
}

function parse_content_for_variables($text)
{
    if (strpos($text, '{') === FALSE) return $text;

    // parse the data
    // The following are valid: {year}, {month}, {day}, {dayord}, {dow}, {date}
    // full numeric year: 2024
    if (strpos($text, '{year}') !== FALSE) {
        return str_replace('{year}', date("Y"), $text);
    }

    // full text month: January
    if (strpos($text, '{month}') !== FALSE) {
        return str_replace('{month}', date("F"), $text);
    }

    // numeric date: 27
    if (strpos($text, '{day}') !== FALSE) {
        return str_replace('{day}', date("j"), $text);
    }

    // numeric day with ordinal: 27th
    if (strpos($text, '{dayord}') !== FALSE) {

        $num = date("j");
        $ones = $num % 10;
        $tens = floor($num / 10) % 10;
        if ($tens == 1) {
            $suff = "th";
        } else {
            switch ($ones) {
                case 1:
                    $suff = "st";
                    break;
                case 2:
                    $suff = "nd";
                    break;
                case 3:
                    $suff = "rd";
                    break;
                default:
                    $suff = "th";
            }
        }
        return str_replace('{dayord}', $num . $suff, $text);
    }

    // full text day of week: Wednesday
    if (strpos($text, '{dow}') !== FALSE) {
        return str_replace('{dow}', date("l"), $text);
    }

    // full date: 2024-08-27
    if (strpos($text, '{date}', 0) !== FALSE) {
        return str_replace('{date}', date("Y-m-d"), $text);
    }

    return $text;
}

function reorderActions(array &$rules, int $ruleIndex, int $oldIndex, int $newIndex): bool
{
    if (!isset($rules[$ruleIndex]['actions'][$oldIndex])) {
        return false; // Invalid index
    }

    // Extract the item to move
    $item = $rules[$ruleIndex]['actions'][$oldIndex];

    // Remove it from old position
    array_splice($rules[$ruleIndex]['actions'], $oldIndex, 1);

    // Insert it at the new position
    array_splice($rules[$ruleIndex]['actions'], $newIndex, 0, [$item]);

    return true;
}

function array_to_html($val, $var = FALSE)
{
    $do_nothing = true;
    $indent_size = 20;
    $out = '';
    $colors = array(
        "Teal",
        "YellowGreen",
        "Tomato",
        "Navy",
        "MidnightBlue",
        "FireBrick",
        "DarkGreen"
    );

    // Get string structure
    ob_start();
    print_r($val);
    $val = ob_get_contents();
    ob_end_clean();

    // Color counter
    $current = 0;

    // Split the string into character array
    $array = preg_split('//', $val, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($array as $char) {
        if ($char == "[")
            if (!$do_nothing)
                if ($var) {
                    $out .= "</div>";
                } else {
                    echo "</div>";
                }
            else $do_nothing = false;
        if ($char == "[")
            if ($var) {
                $out .= "<div>";
            } else {
                echo "<div>";
            }
        if ($char == ")") {
            if ($var) {
                $out .= "</div></div>";
            } else {
                echo "</div></div>";
            }
            $current--;
        }

        if ($var) {
            $out .= $char;
        } else {
            echo $char;
        }

        if ($char == "(") {
            if ($var) {
                $out .= "<div class='indent' style='padding-left: {$indent_size}px; color: " . ($colors[$current % count($colors)]) . ";'>";
            } else {
                echo "<div class='indent' style='padding-left: {$indent_size}px; color: " . ($colors[$current % count($colors)]) . ";'>";
            }
            $do_nothing = true;
            $current++;
        }
    }

    return $out;
}

function getEvernoteTokenExpiry($token)
{
    if (preg_match('/E=([0-9a-f]+)/', $token, $matches)) {
        $ms = hexdec($matches[1]);
        return (int)($ms / 1000); // return as Unix timestamp (seconds)
    }
    return null;
}

function checkEvernoteToken($token, $warningDays = 7)
{
    $expiry = getEvernoteTokenExpiry($token);

    if (!$expiry) {
        return [
            'status' => 'invalid',
            'message' => 'Could not parse token expiry'
        ];
    }

    $now = time();
    $daysLeft = ($expiry - $now) / 86400;

    if ($expiry < $now) {
        return [
            'status' => 'expired',
            'message' => 'Token has expired',
            'expiry_date' => date('Y-m-d H:i:s', $expiry)
        ];
    }

    if ($daysLeft <= $warningDays) {
        return [
            'status' => 'warning',
            'message' => 'Token expiring soon',
            'days_left' => floor($daysLeft),
            'expiry_date' => date('Y-m-d H:i:s', $expiry)
        ];
    }

    return [
        'status' => 'ok',
        'message' => 'Token is valid',
        'days_left' => floor($daysLeft),
        'expiry_date' => date('Y-m-d H:i:s', $expiry)
    ];
}
