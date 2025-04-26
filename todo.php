<?php
$page_title = "To-Do List";
include_once 'includes/storage.php';

$filename = getUserFilename('todo');
$tasks = loadData($filename);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_task'])) {
        $newTask = trim($_POST['new_task']);
        if (!empty($newTask)) {
            $tasks[] = [
                'id' => uniqid(),
                'task' => $newTask,
                'created' => time()
            ];
            saveData($filename, $tasks);
        }
    }
    
    if (isset($_POST['delete_id'])) {
        $tasks = array_filter($tasks, function($task) {
            return $task['id'] !== $_POST['delete_id'];
        });
        saveData($filename, $tasks);
    }
    
    // Prevent form resubmission
    header("Location: todo.php");
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
        .todo-container {
            background-color: var(--vintage-paper);
            padding: 2rem;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: 1px solid var(--vintage-border);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .todo-container input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            font-family: 'EB Garamond', serif;
            font-size: 1.1rem;
            background-color: var(--vintage-light);
            border: 1px solid var(--vintage-border);
            color: var(--vintage-text);
            margin-bottom: 1rem;
        }
        
        .todo-container button[type="submit"] {
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
        
        .todo-container button[type="submit"]:hover {
            background-color: var(--vintage-accent);
        }
        
        .task-list {
            margin-top: 2rem;
            list-style: none;
            padding: 0;
        }
        
        .task-item {
            background-color: var(--vintage-light);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: 1px solid var(--vintage-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-item span {
            font-family: 'EB Garamond', serif;
            font-size: 1.1rem;
            color: var(--vintage-text);
            flex-grow: 1;
        }
        
        .task-item button {
            background-color: var(--vintage-error);
            color: var(--vintage-light);
            border: none;
            padding: 0.5rem 1rem;
            font-family: 'Cormorant Garamond', serif;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .task-item button:hover {
            background-color: #a03a3a;
        }
    </style>
</head>
<body class="vintage-theme">
    <div class="container">
        <div class="vintage-overlay"></div>
        <div class="vintage-texture"></div>
        
        <h1 class="vintage-header">To-Do List</h1>
        
        <div class="todo-container">
            <form method="POST">
                <input type="text" name="new_task" placeholder="Add a new task..." required>
                <button type="submit">Add Task</button>
            </form>
            
            <ul class="task-list">
                <?php foreach ($tasks as $task): ?>
                <li class="task-item">
                    <span><?= htmlspecialchars($task['task']) ?></span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>