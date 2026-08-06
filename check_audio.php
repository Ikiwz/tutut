<?php
$db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$hasDirs = $db->query('SELECT COUNT(*) FROM directions WHERE audio_path IS NOT NULL AND audio_path != ""')->fetchColumn();
echo "Directions with audio: $hasDirs\n";
if ($hasDirs > 0) {
    print_r($db->query('SELECT id, label, audio_path FROM directions WHERE audio_path IS NOT NULL AND audio_path != ""')->fetchAll(PDO::FETCH_ASSOC));
}

$hasQs = $db->query('SELECT COUNT(*) FROM questions WHERE audio_path IS NOT NULL AND audio_path != ""')->fetchColumn();
echo "Questions with audio: $hasQs\n";
if ($hasQs > 0) {
    print_r($db->query('SELECT id, audio_path FROM questions WHERE audio_path IS NOT NULL AND audio_path != ""')->fetchAll(PDO::FETCH_ASSOC));
}

// Find any actual files in public or storage directories
function findMp3($dir) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['mp3', 'wav', 'm4a', 'ogg'])) {
            echo "Found audio file: " . $file->getPathname() . "\n";
        }
    }
}
findMp3(__DIR__ . '/public');
findMp3(__DIR__ . '/storage');
