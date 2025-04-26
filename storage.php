<?php
// File-based storage system
function saveData($filename, $data) {
    file_put_contents($filename, json_encode($data));
}

function loadData($filename) {
    if (file_exists($filename)) {
        return json_decode(file_get_contents($filename), true) ?: [];
    }
    return [];
}

// Simple session-based user identification
function getUserId() {
    if (!session_id()) {
        session_start();
    }
    return session_id();
}

function getUserFilename($prefix) {
    $userId = getUserId();
    return "data/{$prefix}_{$userId}.json";
}

// Ensure data directory exists
if (!file_exists('data')) {
    mkdir('data');
}
?>