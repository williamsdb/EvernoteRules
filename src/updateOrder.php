<?php
header('Content-Type: application/json');

require 'functions.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$oldIndex = $data['oldIndex'] ?? null;
$newIndex = $data['newIndex'] ?? null;
$id = $data['ruleId'] ?? null;

// Have we got all the require parameters?
if (!isset($oldIndex, $newIndex, $id)) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

// is there anything for us to do?
if ($oldIndex == $newIndex) {
    echo json_encode(['success' => true]);
    exit;
}

// execute the change
try {
    // update the order in the array
    if (reorderActions($_SESSION['rules'], $id, $oldIndex, $newIndex)) {
        // store the rules in the rules database file
        writeRules($_SESSION['rules']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid rule or action index']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
