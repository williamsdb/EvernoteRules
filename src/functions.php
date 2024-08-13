<?php

    function pushover($message, $token, $user) {
        // Send to PushOver
        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
            CURLOPT_POSTFIELDS => array(
            "token" => $token,
            "user" => $user,
            "message" => $message,
            ),
        ));
        curl_exec($ch);
        curl_close($ch);

        return;
    }

    function write_oauth_key($oauth, $file) {

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

    function getNotebooks($smarty) {

        $client = new \Evernote\Client(OAUTH, FALSE, null, null, FALSE);

        $notebooks = array();
        try {

            // Process the list of notebooks
            $notebooks = $client->listNotebooks();

        } catch (EDAMUserException $e) {

            if ($e->errorCode === EDAMErrorCode::AUTH_EXPIRED) {
                $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
                die;
            } else {
                // Handle other exceptions
                $smarty->assign('error', 'An error occurred: ' . $e->getMessage());
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
                die;
            }

        }
        
        if (empty($notebooks)){
            $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
            die;
        }else{

            foreach ($notebooks as $notebook) {
                $result[] = array("guid" => $notebook->guid, "name" => $notebook->name);
            }

        }

        usort($result, 'compareByName');
        return $result;

    }

    function getTags($client) {

        $tags = array();
        try {

            // Process the list of tags
            $tags = $client->listTags();

        } catch (EDAMUserException $e) {

            if ($e->errorCode === EDAMErrorCode::AUTH_EXPIRED) {
                $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
                die;
            } else {
                // Handle other exceptions
                $smarty->assign('error', 'An error occurred: ' . $e->getMessage());
                $smarty->assign('oauth', OAUTH);
                $smarty->display('home.tpl');
                die;
            }

        }
        
        if (empty($tags)){
            $smarty->assign('error', 'Token has expired. <a href="/oauth">Click here to regenerate</a>');
            $smarty->assign('oauth', OAUTH);
            $smarty->display('home.tpl');
            die;
        }else{
            foreach ($tags as $tag) {
                $result[] = array("guid" => $tag->guid, "name" => $tag->name);
            }

        }

        usort($result, 'compareByName');
        return $result;

    }

    function compareByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    function findNameByGuid($array, $guid) {
        foreach ($array as $element) {
            if ($element['guid'] === $guid) {
                return $element['name'];
            }
        }
        // Return null or an appropriate value if the guid is not found
        return null;
    }

    function findGuidByName($array, $name) {
        foreach ($array as $element) {
            if ($element['name'] === $name) {
                return $element['guid'];
            }
        }
        // Return null or an appropriate value if the guid is not found
        return null;
    }

    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    function readRules() {
        // Read the rules database
        $rules = file_get_contents('./rules.db');
        return unserialize($rules);
    }

    function writeRules($rules) {
        // write the rules to the database file
        file_put_contents('./rules.db',serialize($rules));
    }

    function checkTitleCondition($title,  $condition, $conditionText){

        // does the title given meet the condition?
        if ($condition == '0'){
            return TRUE;
        }if ($condition == '1' && $title == $conditionText){
            return TRUE;
        }if ($condition == '2' && str_contains($title, $conditionText)){
            return TRUE;
        }if ($condition == '3' && startsWith($title, $conditionText)!=''){
            return TRUE;
        }if ($condition == '4' && endsWith($title, $conditionText)!=''){
            return TRUE;
        }

        return FALSE;

    }

    function checkAuthorCondition($author, $authorText){

        // does the author text given meet the condition?
        if (str_contains($author, $authorText)) return TRUE;

        return FALSE;

    }

    function checkTagCondition($tags, $conditionTags){

        // we only need to check if there are condition tags and note tags
        if ((empty($tags) && empty($conditionTags)) || (!empty($tags) && empty($conditionTags))) return TRUE;
        if ((empty($tags) && !empty($conditionTags))) return FALSE;

        // turn the comma separated list into an array
        $condTags = explode(',', $conditionTags);

        // walk through the array seeing if these tags exists on the note itself
        $i=0;
        $state = 0;
        while ($i <= count($condTags)) {
            $j=0;
            while ($j <= count($tags)) {
                if (trim($condTags[$i])==$tags[$j]) $state++;
                $j++;
            }                
            $i++;
        }

        if ($state==count($condTags)){
            return TRUE;
        }else{
            return FALSE;
        }

    }

    function processActions($actions, $ruleName, $title, $client, $note, $noteStore, $noteGuid){

        // get current tags
        $tags = getTags($noteStore);

        // cycle through the actions 
        for ($i = 0; $i < count($actions); $i++) {

            // process the action
            switch($actions[$i]['option']) {

                // move to notebook
                case 'move':

                    try {
                        $notebook = new \Evernote\Model\Notebook();
                        $notebook->guid = $actions[$i]['moveNotebookGuid'];
                        $moved_note = $client->moveNote($note, $notebook);

                        debug("Note moved successfully.");
                    } catch (Exception $e) {
                        debug('Error moving note: '.  $e->getMessage());
                    }

                // change the title
                case 'subject':

                    // Replace the text
                    $new_contents = str_replace($actions[$i]['subjectFind'], $actions[$i]['subjectReplace'], $title);

                    try {
                        $ret = $client->getNote($noteGuid);
                        $edamNote = $ret->getEdamNote();
                        $edamNote->title = $new_contents;
            
                        // Update the note on the server
                        $noteStore = $client->getAdvancedClient()->getNoteStore();
                        $updatedNote = $noteStore->updateNote(OAUTH, $edamNote);
                          
                        debug("Note updated successfully! New title: " . $updatedNote->title);
                    } catch (Exception $e) {
                        debug('Error updating note: '.  $e->getMessage());
                    }
                                
                    break;

                // add tags
                case 'tags':

                    // are there any tags specified?
                    if (count($actions[$i]['tags'])==0) break;

                    // cycle through the tags to format them
                    $tagsToAdd = [];
                    $j = 0;
                    while ($j < count($actions[$i]['tags'])) {

                        // are there any variables?
                        $actions[$i]['tags'][$j] = parse_content_for_variables($actions[$i]['tags'][$j]);

                        // does the tag already exist, if not create
                        $tag = findGuidByName($tags, $actions[$i]['tags'][$j]);
                        if ($tag == ''){
                            //create tag
                            $newTag = new \EDAM\Types\Tag;
                            $newTag->name = $actions[$i]['tags'][$j]; // Set the desired tag name
                            
                            try {
                                $createdTag = $noteStore->createTag(OAUTH, $newTag);
                                debug("Tag created successfully. Tag GUID: " . $createdTag->guid);
                            } catch (\EDAM\Error\EDAMUserException $e) {
                                if ($e->errorCode == \EDAM\Error\EDAMErrorCode::DATA_CONFLICT) {
                                    debug("A tag with this name already exists. ".$actions[$i]['tags'][$j]);
                                } else {
                                    debug("An error occurred: " . $e->getMessage());
                                }
                            } catch (\Exception $e) {
                                debug("An error occurred: " . $e->getMessage());
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
                        $edamNote->tagGuids = array_merge($edamNote->tagGuids, $tagsToAdd);
                
                        // Remove duplicates by converting to array keys and back
                        $edamNote->tagGuids = array_values(array_unique($edamNote->tagGuids));
                
                        // Update the note on the server
                        $updatedNote = $noteStore->updateNote(OAUTH, $edamNote);
                        
                        echo "Note updated successfully! New title: " . $updatedNote->title;

                    } catch (Exception $e) {

                        echo 'Error updating note: ',  $e->getMessage(), "\n";

                    }

                    break;

                // send pushover notification
                case 'pushover':
                        
                    return pushover('Rule '.$ruleName.' has just been triggered', PUSHOVER_TOKEN, PUSHOVER_USER);
                    break;
                    
                // delete the note
                case 'delete':

                    $client->deleteNote($note);						
                    break;

                // bad case
                default:
                    
                    return 'Action id '.$actions[$i]['option'].' has not been recognised';
                    break;
            }

        }

    }

    // log calls
    function debug($string){

        if (!DEBUG) return;

        // write the rules to the database file
        file_put_contents('./logs.db', date("Y-m-d H:i:s").','.'"'.$string.'"'.PHP_EOL, FILE_APPEND);

    }

    function parse_content_for_variables($text)
    {
  
      if (strpos($text, '{') === FALSE) return $text;
    
      // parse the data
      // The following are valid: {year}, {month}, {day}, {dayord}, {dow}, {date}
  
      // full numeric year: 2024
      if (strpos($text, '{year}')>=0){
        return str_replace('{year}', date("Y"), $text);
      }

      // full text month: January
      if (strpos($text, '{month}')>=0){
        return str_replace('{month}', date("F"), $text);
      }

      // numeric date: 27
      if (strpos($text, '{day}')>=0){
        return str_replace('{day}', date("d"), $text);
      }

      // numeric day with ordinal: 27th
      if (strpos($text, '{dayord}')>=0){
  
        $num = date("d");
        $ones = $num % 10;
        $tens = floor($num / 10) % 10;
        if ($tens == 1) {
            $suff = "th";
        } else {
            switch ($ones) {
                case 1 : $suff = "st"; break;
                case 2 : $suff = "nd"; break;
                case 3 : $suff = "rd"; break;
                default : $suff = "th";
            }
        }
        return str_replace('{dayord}', $num . $suff, $text);
  
      }

      // full text day of week: Wednesday
      if (strpos($text, '{dow}')>=0){
        return str_replace('{dow}', date("l"), $text);
      }

      // full date: 2024-08-27
      if (strpos($text, '{date}', 0)>=0){
        return str_replace('{date}', date("Y-m-d"), $text);
      }
  
      return $text;
  
    }
  
    function array_to_html($val) {
        $do_nothing = true;
        $indent_size = 20;
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
          foreach($array as $char) {
              if($char == "[")
                  if(!$do_nothing)
                      echo "</div>";
                  else $do_nothing = false;
              if($char == "[")
                  echo "<div>";
              if($char == ")") {
                  echo "</div></div>";
                  $current--;
              }
      
              echo $char;
      
              if($char == "(") {
                  echo "<div class='indent' style='padding-left: {$indent_size}px; color: ".($colors[$current % count($colors)]).";'>";
                  $do_nothing = true;
                  $current++;
              }
          }
    }
      
?>