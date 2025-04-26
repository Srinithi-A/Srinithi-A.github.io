<?php
$page_title = "My Diary";
include_once 'includes/storage.php';

$filename = getUserFilename('diary');
$entries = loadData($filename);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diary_entry'])) {
    $newEntry = trim($_POST['diary_entry']);
    if (!empty($newEntry)) {
        $entries[] = [
            'id' => uniqid(),
            'date' => date('Y-m-d H:i:s'),
            'content' => $newEntry
        ];
        saveData($filename, $entries);
    }
    
    // Prevent form resubmission
    header("Location: diary.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=EB+Garamond:wght@400;500&display=swap">
    <link rel="stylesheet" href="style.css">
    <style>
        .diary-container {
            background-color: var(--vintage-paper);
            padding: 2rem;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: 1px solid var(--vintage-border);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .diary-container textarea {
            width: 100%;
            min-height: 200px;
            padding: 1rem;
            font-family: 'EB Garamond', serif;
            font-size: 1.1rem;
            background-color: var(--vintage-light);
            border: 1px solid var(--vintage-border);
            color: var(--vintage-text);
            margin-bottom: 1rem;
            resize: vertical;
        }
        
        .diary-container button {
            background-color: var(--vintage-secondary);
            color: var(--vintage-light);
            border: none;
            padding: 0.8rem 1.5rem;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 4px;
        }
        
        .diary-container button:hover {
            background-color: var(--vintage-accent);
        }
        
        .diary-entries {
            margin-top: 2rem;
        }
        
        .diary-entry {
            background-color: var(--vintage-light);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            border: 1px solid var(--vintage-border);
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .diary-date {
            font-family: 'Cormorant Garamond', serif;
            color: var(--vintage-secondary);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .diary-content {
            font-family: 'EB Garamond', serif;
            line-height: 1.6;
            color: var(--vintage-text);
            white-space: pre-wrap;
        }
    </style>
</head>
<body class="vintage-theme">
    <div class="container">
        <div class="vintage-overlay"></div>
        <div class="vintage-texture"></div>
        
        <h1 class="vintage-header">My Diary</h1>
        
        <div class="diary-container">
            <form method="POST">
                <textarea name="diary_entry" placeholder="Write your diary entry here..." required></textarea>
                <button type="submit">Save Entry</button>
            </form>
            
            <div class="diary-entries">
                <?php foreach (array_reverse($entries) as $entry): ?>
                <div class="diary-entry">
                    <div class="diary-date"><?= htmlspecialchars($entry['date']) ?></div>
                    <div class="diary-content"><?= nl2br(htmlspecialchars($entry['content'])) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>