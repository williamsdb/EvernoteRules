<?php

/**
 * index.php
 *
 * Check all notes in a notebook and apply rules.
 *
 * @author     Neil Thompson <neil@spokenlikeageek.com>
 * @copyright  2024 Neil Thompson
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License v3.0
 * @link       https://github.com/williamsdb/EvernoteRules
 * @see        https://www.spokenlikeageek.com/2023/08/02/exporting-all-wordpress-posts-to-pdf/ Blog post
 * 
 * ARGUMENTS
 * Notebook         Name of the notebook to scan
 * Rules            location and name of file of rules to process on notebook
 *
 */


// session start
session_start();

// Load Composer & parameters
require 'vendor/autoload.php';
require 'config.php';
require 'functions.php';

// set up Smarty
use Smarty\Smarty;

$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('configs');
$smarty->use_sub_folders = TRUE;

// turn off reporting of notices
error_reporting(E_ALL);
ini_set('display_errors', 1);

// load up the list of notebooks
if (empty($_SESSION['notebooks']) && OAUTH != '') {
    $_SESSION['notebooks'] = getNotebooks($smarty);
}

// get the existing rules
if (empty($_SESSION['rules'])) {
    $_SESSION['rules'] = readRules();
}

// any error or information messages
if (!empty($_SESSION['error'])) {
    $smarty->assign('error', $_SESSION['error']);
    unset($_SESSION['error']);
}

// Get the current path from the requested URL
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove leading and trailing slashes
$trimmed_path = trim($current_path, '/');

// Split the path into segments
$path_segments = explode('/', $trimmed_path);

// Get the first segment, which is the command, followed by the rule id and then the action id
$cmd = $path_segments[0];
if (isset($path_segments[1])){
    $id = $path_segments[1];
}
if (isset($path_segments[2])){
    $act = $path_segments[2];
}

// execute command
switch ($cmd) {
    case 'oauth':
        $oauth_handler = new \Evernote\Auth\OauthHandler(FALSE);

        $callback = CALLBACK_URL . '/oauth/';

        $oauth_data  = $oauth_handler->authorize(KEY, SECRET, $callback);

        if (isset($oauth_data['oauth_token'])) {

            write_oauth_key($oauth_data['oauth_token'], './config.php');

            $smarty->assign('error', 'Evernote authorised');
            $smarty->assign('oauth', OAUTH);
            $smarty->assign('rules', $_SESSION['rules']);
            $smarty->display('home.tpl');
            die;
        } else {

            $smarty->assign('error', 'Problem authorising Evernote');
            $smarty->assign('oauth', OAUTH);
            $smarty->assign('rules', $_SESSION['rules']);
            $smarty->display('home.tpl');
            die;
        }

        break;

    case 'database':

        $smarty->assign('list', array_to_html($_SESSION['rules'], TRUE));
        $smarty->display('list.tpl');
        die;

        break;

    case 'notebooks':

        $smarty->assign('notebooks', array_to_html($_SESSION['notebooks'], TRUE));
        $smarty->display('notebooks.tpl');
        die;

        break;

    case 'clearcache':

        session_destroy();
		Header('Location: /');
        die;

        break;

    case 'log':

        $log = file_get_contents("./logs.db");
        $logarr = explode("\n", $log);
        $log = [];
        $i = count($logarr);
        $j = 0;
        while ($i >= 0 && $j <= 100){
            if (!empty($logarr[$i])){
                $t = explode(",", $logarr[$i]);
                $log[$j]['date'] = $t[0];
                $log[$j]['entry'] = trim($t[1], "\"");    
                $j++;
            }
            $i--;
        }

        $smarty->assign('log', $log);
        $smarty->display('log.tpl');
        die;

        break;

    case 'addRule':

        $smarty->assign('notebooks', $_SESSION['notebooks']);
        $smarty->display('addRule.tpl');
        break;

    case 'createRule':

        if (!isset($_SESSION['rules']) || !is_array($_SESSION['rules'])) {
            $_SESSION['rules'] = array();
            $_SESSION['rules'][0]['ruleName'] = $_REQUEST['ruleName'];
            $_SESSION['rules'][0]['type'] = $_REQUEST['type'];
            $_SESSION['rules'][0]['condition'] = $_REQUEST['condition'];
            $_SESSION['rules'][0]['conditionText'] = $_REQUEST['conditionText'];
            $_SESSION['rules'][0]['authorText'] = $_REQUEST['authorText'];
            $_SESSION['rules'][0]['notebookGuid'] = $_REQUEST['notebook'];
            $notebookName = findNameByGuid($_SESSION['notebooks'], $_REQUEST['notebook']);
            $_SESSION['rules'][0]['notebookName'] = $notebookName;
            $_SESSION['rules'][0]['tags'] = $_REQUEST['tags'];
            $_SESSION['rules'][0]['actions'] = array();

        }else{
            $i = count($_SESSION['rules']);
            $_SESSION['rules'][$i]['ruleName'] = $_REQUEST['ruleName'];
            $_SESSION['rules'][$i]['type'] = $_REQUEST['type'];
            $_SESSION['rules'][$i]['condition'] = $_REQUEST['condition'];
            $_SESSION['rules'][$i]['conditionText'] = $_REQUEST['conditionText'];
            $_SESSION['rules'][$i]['authorText'] = $_REQUEST['authorText'];
            $_SESSION['rules'][$i]['notebookGuid'] = $_REQUEST['notebook'];
            $notebookName = findNameByGuid($_SESSION['notebooks'], $_REQUEST['notebook']);
            $_SESSION['rules'][$i]['notebookName'] = $notebookName;
            $_SESSION['rules'][$i]['tags'] = $_REQUEST['tags'];
            $_SESSION['rules'][$i]['actions'] = array();
        }

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);
        $i = count($_SESSION['rules'])-1;

        // Redirect to the relevant page
        $_SESSION['error'] = 'Rule created';
		Header('Location: /editRule/'.$i);

        break;

    case 'editRule':

        $smarty->assign('ruleName', $_SESSION['rules'][$id]['ruleName']);
        $smarty->assign('type', $_SESSION['rules'][$id]['type']);
        $smarty->assign('notebookGuid', $_SESSION['rules'][$id]['notebookGuid']);
        $smarty->assign('condition', $_SESSION['rules'][$id]['condition']);
        $smarty->assign('conditionText', $_SESSION['rules'][$id]['conditionText']);
        $smarty->assign('authorText', $_SESSION['rules'][$id]['authorText']);
        $smarty->assign('tags', $_SESSION['rules'][$id]['tags']);
        $smarty->assign('notebooks', $_SESSION['notebooks']);
        $smarty->assign('actions', $_SESSION['rules'][$id]['actions']);
        $smarty->assign('id', $id);
        $smarty->display('editRule.tpl');
        break;

    case 'deleteRule':

        // delete the rule
        unset($_SESSION['rules'][$id]);
        $_SESSION['rules'] = array_values($_SESSION['rules']);

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);

        $smarty->assign('error', 'Rule deleted');
        $smarty->assign('oauth', OAUTH);
        $smarty->assign('rules', $_SESSION['rules']);
        $smarty->display('home.tpl');

        break;

    case 'updateRule':

        $_SESSION['rules'][$id]['ruleName'] = $_REQUEST['ruleName'];
        $_SESSION['rules'][$id]['type'] = $_REQUEST['type'];
        $_SESSION['rules'][$id]['condition'] = $_REQUEST['condition'];
        $_SESSION['rules'][$id]['conditionText'] = $_REQUEST['conditionText'];
        $_SESSION['rules'][$id]['authorText'] = $_REQUEST['authorText'];
        $_SESSION['rules'][$id]['notebookGuid'] = $_REQUEST['notebook'];
        $notebookName = findNameByGuid($_SESSION['notebooks'], $_REQUEST['notebook']);
        $_SESSION['rules'][$id]['notebookName'] = $notebookName;
        $_SESSION['rules'][$id]['tags'] = $_REQUEST['tags'];

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Rule updated';
		Header('Location: /editRule/'.$id);

        break;

    case 'addAction':

        $smarty->assign('notebooks', $_SESSION['notebooks']);
        $smarty->assign('id', $id);
        $smarty->display('addAction.tpl');
        break;

    case 'createAction':

        $i = count($_SESSION['rules'][$id]['actions']);
        $_SESSION['rules'][$id]['actions'][$i]['option'] = $_REQUEST['option'];
        $_SESSION['rules'][$id]['actions'][$i]['moveNotebookGuid'] = $_REQUEST['moveNotebook'];
        $notebookName = findNameByGuid($_SESSION['notebooks'], $_REQUEST['moveNotebook']);
        $_SESSION['rules'][$id]['actions'][$i]['moveNotebookName'] = $notebookName;
        $_SESSION['rules'][$id]['actions'][$i]['subjectFind'] = $_REQUEST['subjectFind'];
        $_SESSION['rules'][$id]['actions'][$i]['subjectReplace'] = $_REQUEST['subjectReplace'];
        $_SESSION['rules'][$id]['actions'][$i]['tags'] = $_REQUEST['tags'];

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);
        $i = count($_SESSION['rules'])-1;

        // Redirect to the relevant page
        $_SESSION['error'] = 'Action created';
		Header('Location: /editRule/'.$i);

        break;

    case 'editAction':

        $smarty->assign('option', $_SESSION['rules'][$id]['actions'][$act]['option']);
        $smarty->assign('moveNotebook', $_SESSION['rules'][$id]['actions'][$act]['moveNotebookGuid']);
        $smarty->assign('subjectFind', $_SESSION['rules'][$id]['actions'][$act]['subjectFind']);
        $smarty->assign('subjectReplace', $_SESSION['rules'][$id]['actions'][$act]['subjectReplace']);
        $smarty->assign('conditionText', $_SESSION['rules'][$id]['conditionText']);
        $smarty->assign('tags', $_SESSION['rules'][$id]['actions'][$act]['tags']);
        $smarty->assign('notebooks', $_SESSION['notebooks']);
        $smarty->assign('id', $id);
        $smarty->assign('act', $act);
        $smarty->display('editAction.tpl');
        break;

    case 'deleteAction':

        // delete the action
        unset($_SESSION['rules'][$id]['actions'][$act]);
        $_SESSION['rules'][$id]['actions'] = array_values($_SESSION['rules'][$id]['actions']);

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Action deleted';
		Header('Location: /editRule/'.$id);

        break;

    case 'updateAction':

        $_SESSION['rules'][$id]['actions'][$act]['option'] = $_REQUEST['option'];
        $_SESSION['rules'][$id]['actions'][$act]['moveNotebookGuid'] = $_REQUEST['moveNotebook'];
        $notebookName = findNameByGuid($_SESSION['notebooks'], $_REQUEST['moveNotebook']);
        $_SESSION['rules'][$id]['actions'][$act]['moveNotebookName'] = $notebookName;
        $_SESSION['rules'][$id]['actions'][$act]['subjectFind'] = $_REQUEST['subjectFind'];
        $_SESSION['rules'][$id]['actions'][$act]['subjectReplace'] = $_REQUEST['subjectReplace'];
        $_SESSION['rules'][$id]['actions'][$act]['tags'] = $_REQUEST['tags'];

        // store the rules in the rules database file
        writeRules($_SESSION['rules']);

        // Redirect to the relevant page
        $_SESSION['error'] = 'Action updated';
		Header('Location: /editRule/'.$id);

        break;

    case 'webhook':

        // get the reason we are being polled
        $reason = $_REQUEST['reason'];
        
        // action depending on the event type
        switch($reason)
        {

            // Create Notebook
            // [base URL]/?userId=[user ID]&notebookGuid=[notebook GUID]&reason=notebook_create
            case 'notebook_create':

                // not interested so discard

            break;

            // Update Notebook
            // [base URL]/?userId=[user ID]&notebookGuid=[notebook GUID]&reason=notebook_update
            case 'notebook_update':

                // not interested so discard

            break;

            // Update or Create Note
            // [base URL]/?userId=[user ID]&guid=[note GUID]&notebookGuid=[notebook GUID]&reason=create
            case 'update':
            case 'create':

                // get the details
                $userId = $_REQUEST['userId'];
                $noteGuid = $_REQUEST['guid'];
                $notebookGuid = $_REQUEST['notebookGuid'];

                // cycle through the rules finding any matches.
                $i = 0;
                while ($i <= count($_SESSION['rules'])-1) {
                    if ((($_SESSION['rules'][$i]['type'] == 'Created' && $reason == 'create') || ($_SESSION['rules'][$i]['type'] == 'Updated' && $reason == 'update')) &&
                         ($_SESSION['rules'][$i]['notebookGuid'] == $notebookGuid || $_SESSION['rules'][$i]['notebookGuid'] =='')){
                        // we have a note that matches so get the details
                        $client = new \Evernote\Client(OAUTH, FALSE);
                        $noteStore = $client->getAdvancedClient()->getNoteStore();
                        $note = $client->getNote($noteGuid);
                        $title = $note->title;
                        $author = $note->attributes->author;
                        $tags = $noteStore->getNoteTagNames($noteGuid);

                        // does this note meet all the conditions?
                        $titleRes = checkTitleCondition($title,  $_SESSION['rules'][$i]['condition'], $_SESSION['rules'][$i]['conditionText']);
                        $authorRes = checkAuthorCondition($author, $_SESSION['rules'][$i]['authorText']);
                        $tagRes = checkTagCondition($tags, $_SESSION['rules'][$i]['tags']);

                        // do we need to take action?
                        if ($titleRes && $authorRes && $tagRes){
                            // process actions
                            processActions($_SESSION['rules'][$i]['actions'], $_SESSION['rules'][$i]['ruleName'], $title, $client, $note, $noteStore, $noteGuid);
                        }
                    }
                    $i++;
                }
        
            break;

            // Create Business Notebook
            //[base URL]/?userId=[user ID]&notebookGuid=[notebook GUID]&reason=business_notebook_create
            case 'business_notebook_create':

                // not interested so discard

            break;

            // Update Business Notebook
            //[base URL]/?userId=[user ID]&notebookGuid=[notebook GUID]&reason=business_notebook_update
            case 'business_notebook_create':

                // not interested so discard

            break;

            // Create Business Note
            //[base URL]/?userId=[user ID]&guid=[note GUID]&notebookGuid=[notebook GUID]&reason=business_create
            case 'business_create':

                // not interested so discard

            break;

            // Update Business Note
            //[base URL]/?userId=[user ID]&guid=[note GUID]&notebookGuid=[notebook GUID]&reason=business_update
            case 'business_update':

                // not interested so discard

            // Unknown event type or unprocessed
            default:
                
                //do nothing for now
        }

        break;

    case '':
        $smarty->assign('oauth', OAUTH);
        $smarty->assign('rules', $_SESSION['rules']);
        $smarty->display('home.tpl');
        break;

    default:
        # command not recognised
        $smarty->assign('error', 'Command not recognised');
        $smarty->assign('oauth', OAUTH);
        $smarty->assign('rules', $_SESSION['rules']);
        $smarty->display('home.tpl');
        break;
}
die;
