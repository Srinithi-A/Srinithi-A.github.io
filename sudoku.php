<?php
$page_title = "Vintage Sudoku";
include_once 'includes/storage.php';
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
        :root {
            --vintage-paper: #F0E5D3;
            --vintage-secondary: #8B5A2B;
            --vintage-light: #F5E9D9;
            --vintage-text: #5C4D3D;
            --vintage-accent: #D4A373;
            --vintage-border: #C4B6A0;
            --vintage-error: #8B2E2E;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }

        .vintage-header {
            text-align: center;
            font-family: 'Cormorant Garamond', serif;
            color: var(--vintage-secondary);
            margin-bottom: 20px;
        }

        .vintage-sudoku-container {
            background-color: var(--vintage-paper);
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border: 1px solid var(--vintage-border);
        }

        #sudoku-board {
            display: grid;
            grid-template-columns: repeat(9, 1fr);
            grid-template-rows: repeat(9, 1fr);
            gap: 1px;
            background-color: var(--vintage-secondary);
            border: 2px solid var(--vintage-secondary);
            margin: 20px auto;
            width: 450px;
            height: 450px;
        }
        
        .cell {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: var(--vintage-paper);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            font-family: 'Cormorant Garamond', serif;
            color: var(--vintage-text);
            position: relative;
            user-select: none;
        }
        
        .cell.fixed {
            background-color: var(--vintage-light);
            color: var(--vintage-secondary);
        }
        
        .cell.highlighted {
            background-color: rgba(212, 163, 115, 0.3);
        }
        
        .cell.error {
            color: var(--vintage-error);
        }
        
        /* 3x3 block borders */
        .cell:nth-child(3n) {
            border-right: 2px solid var(--vintage-secondary);
        }
        
        .cell:nth-child(9n) {
            border-right: none;
        }
        
        .cell:nth-child(n+19):nth-child(-n+27),
        .cell:nth-child(n+46):nth-child(-n+54) {
            border-bottom: 2px solid var(--vintage-secondary);
        }

        .vintage-controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .vintage-controls select, 
        .vintage-controls button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid var(--vintage-border);
            background-color: var(--vintage-paper);
            font-family: 'EB Garamond', serif;
            color: var(--vintage-text);
            font-size: 1rem;
        }
        
        .vintage-controls button {
            background-color: var(--vintage-secondary);
            color: var(--vintage-light);
            border: none;
            cursor: pointer;
        }
        
        .vintage-status {
            text-align: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--vintage-accent);
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            #sudoku-board {
                width: 100%;
                height: auto;
                aspect-ratio: 1/1;
            }
            
            .cell {
                font-size: 20px;
            }
        }
    </style>
</head>
<body class="vintage-theme">
    <div class="container">
        <div class="vintage-overlay"></div>
        <div class="vintage-texture"></div>
        
        <h1 class="vintage-header">Vintage Sudoku</h1>
        
        <div class="vintage-sudoku-container">
            <div class="vintage-controls">
                <select id="difficulty">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
                <button id="new-game">New Game</button>
            </div>
            
            <div id="sudoku-board"></div>
            <div class="vintage-status" id="game-status"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const boardElement = document.getElementById('sudoku-board');
            const newGameBtn = document.getElementById('new-game');
            const difficultySelect = document.getElementById('difficulty');
            const gameStatus = document.getElementById('game-status');
            
            let board = Array(9).fill().map(() => Array(9).fill(0));
            let solution = Array(9).fill().map(() => Array(9).fill(0));
            let selectedCell = null;

            // Initialize the game
            newGame();

            // Event listeners
            newGameBtn.addEventListener('click', newGame);
            document.addEventListener('keydown', handleKeyPress);

            function newGame() {
                const difficulty = difficultySelect.value;
                gameStatus.textContent = '';
                generateSudoku(difficulty);
                renderBoard();
            }

            function generateSudoku(difficulty) {
                // Generate solved board first
                generateSolvedBoard();
                
                // Create puzzle by removing numbers based on difficulty
                const cellsToRemove = {
                    easy: 40,   // 41 cells remaining
                    medium: 50, // 31 cells remaining
                    hard: 60    // 21 cells remaining
                }[difficulty] || 50;
                
                let removed = 0;
                while (removed < cellsToRemove) {
                    const row = Math.floor(Math.random() * 9);
                    const col = Math.floor(Math.random() * 9);
                    
                    if (board[row][col] !== 0) {
                        board[row][col] = 0;
                        removed++;
                    }
                }
            }

            function generateSolvedBoard() {
                // Reset boards
                board = Array(9).fill().map(() => Array(9).fill(0));
                solution = Array(9).fill().map(() => Array(9).fill(0));
                
                // Fill diagonal boxes first (they are independent)
                fillDiagonalBoxes(board);
                
                // Then solve the rest of the board
                solveSudoku(board);
                
                // Copy to solution
                for (let i = 0; i < 9; i++) {
                    for (let j = 0; j < 9; j++) {
                        solution[i][j] = board[i][j];
                    }
                }
            }

            function fillDiagonalBoxes(grid) {
                for (let box = 0; box < 9; box += 3) {
                    const nums = shuffleArray([1,2,3,4,5,6,7,8,9]);
                    let index = 0;
                    for (let i = 0; i < 3; i++) {
                        for (let j = 0; j < 3; j++) {
                            grid[box+i][box+j] = nums[index++];
                        }
                    }
                }
            }

            function solveSudoku(grid) {
                for (let row = 0; row < 9; row++) {
                    for (let col = 0; col < 9; col++) {
                        if (grid[row][col] === 0) {
                            for (let num = 1; num <= 9; num++) {
                                if (isValid(grid, row, col, num)) {
                                    grid[row][col] = num;
                                    if (solveSudoku(grid)) {
                                        return true;
                                    }
                                    grid[row][col] = 0;
                                }
                            }
                            return false;
                        }
                    }
                }
                return true;
            }

            function isValid(grid, row, col, num) {
                // Check row
                for (let j = 0; j < 9; j++) {
                    if (grid[row][j] === num) return false;
                }
                
                // Check column
                for (let i = 0; i < 9; i++) {
                    if (grid[i][col] === num) return false;
                }
                
                // Check 3x3 box
                const boxRow = Math.floor(row / 3) * 3;
                const boxCol = Math.floor(col / 3) * 3;
                
                for (let i = 0; i < 3; i++) {
                    for (let j = 0; j < 3; j++) {
                        if (grid[boxRow+i][boxCol+j] === num) return false;
                    }
                }
                
                return true;
            }

            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
                return array;
            }

            function renderBoard() {
                boardElement.innerHTML = '';
                
                for (let i = 0; i < 9; i++) {
                    for (let j = 0; j < 9; j++) {
                        const cell = document.createElement('div');
                        cell.className = 'cell';
                        cell.dataset.row = i;
                        cell.dataset.col = j;
                        
                        if (board[i][j] !== 0) {
                            cell.textContent = board[i][j];
                            cell.classList.add('fixed');
                        }
                        
                        cell.addEventListener('click', () => selectCell(cell, i, j));
                        boardElement.appendChild(cell);
                    }
                }
            }

            function selectCell(cell, row, col) {
                // Deselect previous cell
                if (selectedCell) {
                    selectedCell.classList.remove('highlighted');
                }
                
                // Select new cell if it's empty
                if (board[row][col] === 0) {
                    selectedCell = cell;
                    cell.classList.add('highlighted');
                } else {
                    selectedCell = null;
                }
            }

            function handleKeyPress(e) {
                if (!selectedCell) return;
                
                const row = parseInt(selectedCell.dataset.row);
                const col = parseInt(selectedCell.dataset.col);
                const key = e.key;
                
                // Clear cell
                if (key === '0' || key === 'Delete' || key === 'Backspace') {
                    board[row][col] = 0;
                    selectedCell.textContent = '';
                    selectedCell.classList.remove('error');
                    gameStatus.textContent = '';
                } 
                // Number input (1-9)
                else if (/^[1-9]$/.test(key)) {
                    const num = parseInt(key);
                    board[row][col] = num;
                    selectedCell.textContent = num;
                    selectedCell.classList.remove('fixed');
                    
                    // Check if the number is correct
                    if (solution[row][col] !== num) {
                        selectedCell.classList.add('error');
                        gameStatus.textContent = 'Incorrect number!';
                        gameStatus.style.color = 'var(--vintage-error)';
                    } else {
                        selectedCell.classList.remove('error');
                        checkCompletion();
                    }
                }
            }

            function checkCompletion() {
                for (let i = 0; i < 9; i++) {
                    for (let j = 0; j < 9; j++) {
                        if (board[i][j] !== solution[i][j]) {
                            return;
                        }
                    }
                }
                
                gameStatus.textContent = 'Congratulations! Puzzle solved!';
                gameStatus.style.color = '#4CAF50';
            }
        });
    </script>
</body>
</html>