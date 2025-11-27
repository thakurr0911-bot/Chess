<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chess Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #e6e6e6 0%, #f7f7f7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-family: 'Merriweather', serif;
            font-size: 3rem;
            color: #2c3e50;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        #chessboard {
            width: 504px;
            height: 504px;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            border: 12px solid #443b3bff;
            border-radius: 8px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3),
                inset 0 0 10px rgba(0, 0, 0, 0.2);
            margin: 40px auto;
            position: relative;
        }

        .square {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            position: relative;
            transition: transform 0.1s ease-in-out, background-color 0.3s;
            background-size: cover;
            background-position: center;
        }

        .white {
            background-color: #f3f3f3ff;
            color:black;
        }

        .black {
            background-color: #141414ff;
            color:white;
        }

        .square:hover {
            transform: scale(1.05);
            z-index: 10;
        }

        .selected {
            background-color: rgba(255, 165, 0, 0.7);
            box-shadow: inset 0 0 15px rgba(255, 140, 0, 0.8), 0 0 10px rgba(255, 165, 0, 0.5);
            border: 2px solid #ffcc00;
            transform: scale(1.1);
            z-index: 11;
        }

        .possible-move::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background-color: #f0e1b4ff;
            transform: translate(-50%, -50%);
            border:1px solid orange;
            box-shadow: 0 0 15px rgba(169, 245, 169, 0.8);
        }

        .check {
            background-color: rgba(255, 0, 0, 0.4);
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.7);
            animation: check-pulse 1.5s infinite;
        }

        @keyframes check-pulse {
            0% {
                box-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 0, 0, 1);
            }

            100% {
                box-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
            }
        }

        .game-info {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            border: 1px solid #ddd;
        }

        .btn-danger {
            background-color: #c0392b;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-danger:hover {
            background-color: #e74c3c;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .status-message {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 10px 0;
            min-height: 30px;
            color: #34495e;
        }

        .win-message {
            font-size: 2rem;
            color: #e74c3c;
            font-weight: bold;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
                color: #c0392b;
            }

            100% {
                transform: scale(1);
            }
        }
        .coordinates {
            position: absolute;
            font-size: 10px;
            font-weight: 600;
            opacity: 0.7;
            pointer-events: none;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .file-coordinates {
            bottom: 4px;
            right: 4px;
            color: #fff;
        }

        .rank-coordinates {
            top: 4px;
            left: 4px;
            color: #fff;
        }

        .white-square .file-coordinates,
        .white-square .rank-coordinates {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="text-center">
            <h1 class="mb-4 fw-bold text-primary">Chess Game</h1>
            <div class="status-message" id="status">White's turn</div>
            <div class="win-message" id="winMessage"></div>
            <div id="chessboard"></div>

            <div class="game-info mt-4">
                <button class="btn btn-danger" id="resetBtn">Reset Game</button>
                <div class="mt-3">
                    <strong>Turn:</strong> <span id="turnIndicator">White</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const boardElement = document.getElementById('chessboard');
        const statusElement = document.getElementById('status');
        const winMessageElement = document.getElementById('winMessage');
        const turnIndicator = document.getElementById('turnIndicator');
        const resetBtn = document.getElementById('resetBtn');

        // Unicode symbols for chess pieces
        const pieces = {
            r: '♜',
            n: '♞',
            b: '♝',
            q: '♛',
            k: '♚',
            p: '♟',
            R: '♖',
            N: '♘',
            B: '♗',
            Q: '♕',
            K: '♔',
            P: '♙',
        };

        // Initial board setup
        let boardState = [
            ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'],
            ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
            ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R']
        ];

        // Game state variables
        let currentPlayer = 'white';
        let selectedSquare = null;
        let possibleMoves = [];
        let gameOver = false;
        let whiteKingPos = {
            row: 7,
            col: 4
        };
        let blackKingPos = {
            row: 0,
            col: 4
        };
        let enPassantTarget = null;
        let castlingRights = {
            white: {
                kingSide: true,
                queenSide: true
            },
            black: {
                kingSide: true,
                queenSide: true
            }
        };

        // Render the board
        function renderBoard() {
            boardElement.innerHTML = '';

            // Check if kings are in check
            const whiteInCheck = isSquareUnderAttack(whiteKingPos.row, whiteKingPos.col, 'black');
            const blackInCheck = isSquareUnderAttack(blackKingPos.row, blackKingPos.col, 'white');

            for (let row = 0; row < 8; row++) {
                for (let col = 0; col < 8; col++) {
                    const square = document.createElement('div');
                    square.className = `square ${(row + col) % 2 === 0 ? 'white' : 'black'}`;
                    square.classList.add((row + col) % 2 === 0 ? 'white' : 'black');
                    square.setAttribute('data-row', row);
                    square.setAttribute('data-col', col);

                    // Add coordinates
                    if (col === 7 || row === 0) {
                        const fileCoord = document.createElement('div');
                        fileCoord.className = 'coordinates file-coordinates';
                        fileCoord.textContent = String.fromCharCode(97 + col);
                        square.appendChild(fileCoord);
                    }

                    if (col === 0 || row === 7) {
                        const rankCoord = document.createElement('div');
                        rankCoord.className = 'coordinates rank-coordinates';
                        rankCoord.textContent = 8 - row;
                        square.appendChild(rankCoord);
                    }

                    const piece = boardState[row][col];
                    if (piece) {
                        square.textContent = pieces[piece];
                        square.setAttribute('data-piece', piece);
                    }

                    // Highlight selected square
                    if (selectedSquare && selectedSquare.row === row && selectedSquare.col === col) {
                        square.classList.add('selected');
                    }

                    // Highlight possible moves
                    if (possibleMoves.some(move => move.row === row && move.col === col)) {
                        square.classList.add('possible-move');
                    }

                    // Highlight king in check
                    if ((currentPlayer === 'white' && whiteInCheck && row === whiteKingPos.row && col === whiteKingPos.col) ||
                        (currentPlayer === 'black' && blackInCheck && row === blackKingPos.row && col === blackKingPos.col)) {
                        square.classList.add('check');
                    }

                    square.addEventListener('click', () => handleSquareClick(row, col));
                    boardElement.appendChild(square);
                }
            }

            // Update status
            updateStatus();
        }

        // Handle square clicks
        function handleSquareClick(row, col) {
            if (gameOver) return;

            const piece = boardState[row][col];

            // If a piece is already selected
            if (selectedSquare) {
                // Check if clicking on another piece of the same color
                if (piece && ((currentPlayer === 'white' && isUpperCase(piece)) ||
                        (currentPlayer === 'black' && !isUpperCase(piece)))) {
                    selectedSquare = {
                        row,
                        col
                    };
                    possibleMoves = getPossibleMoves(row, col);
                    renderBoard();
                    return;
                }

                // Check if the move is valid
                const isValidMove = possibleMoves.some(move => move.row === row && move.col === col);

                if (isValidMove) {
                    makeMove(selectedSquare.row, selectedSquare.col, row, col);
                    selectedSquare = null;
                    possibleMoves = [];
                    currentPlayer = currentPlayer === 'white' ? 'black' : 'white';

                    // Check for checkmate or stalemate
                    if (isCheckmate()) {
                        gameOver = true;
                        const winner = currentPlayer === 'white' ? 'Black' : 'White';
                        winMessageElement.textContent = `Checkmate! ${winner} wins!`;
                    } else if (isStalemate()) {
                        gameOver = true;
                        winMessageElement.textContent = "Stalemate! Game is a draw.";
                    } else {
                        // Check for check
                        const kingPos = currentPlayer === 'white' ? whiteKingPos : blackKingPos;
                        if (isSquareUnderAttack(kingPos.row, kingPos.col, currentPlayer === 'white' ? 'black' : 'white')) {
                            statusElement.textContent = `${currentPlayer === 'white' ? 'White' : 'Black'} is in check!`;
                        }
                    }

                    renderBoard();
                    return;
                }
            }

            // Select a piece if it's the current player's turn
            if ((currentPlayer === 'white' && piece && isUpperCase(piece)) ||
                (currentPlayer === 'black' && piece && !isUpperCase(piece))) {
                selectedSquare = {
                    row,
                    col
                };
                possibleMoves = getPossibleMoves(row, col);
                renderBoard();
            }
        }

        // Get possible moves for a piece
        function getPossibleMoves(row, col) {
            const piece = boardState[row][col];
            if (!piece) return [];

            const moves = [];
            const color = isUpperCase(piece) ? 'white' : 'black';
            const enemyColor = color === 'white' ? 'black' : 'white';
            const pieceType = piece.toLowerCase();

            switch (pieceType) {
                case 'p': // Pawn
                    const direction = color === 'white' ? -1 : 1;
                    const startRow = color === 'white' ? 6 : 1;

                    // Forward move
                    if (isValidSquare(row + direction, col) && !boardState[row + direction][col]) {
                        if (!wouldBeInCheck(row, col, row + direction, col)) {
                            moves.push({
                                row: row + direction,
                                col
                            });
                        }

                        // Double move from starting position
                        if (row === startRow && !boardState[row + 2 * direction][col] && !boardState[row + direction][col]) {
                            if (!wouldBeInCheck(row, col, row + 2 * direction, col)) {
                                moves.push({
                                    row: row + 2 * direction,
                                    col
                                });
                            }
                        }
                    }

                    // Captures
                    for (const dc of [-1, 1]) {
                        const newCol = col + dc;
                        if (newCol >= 0 && newCol < 8) {
                            // Normal capture
                            if (isValidSquare(row + direction, newCol) &&
                                boardState[row + direction][newCol] &&
                                (isUpperCase(boardState[row + direction][newCol]) !== isUpperCase(piece))) {
                                if (!wouldBeInCheck(row, col, row + direction, newCol)) {
                                    moves.push({
                                        row: row + direction,
                                        col: newCol
                                    });
                                }
                            }

                            // En passant
                            if (enPassantTarget && enPassantTarget.row === row && enPassantTarget.col === newCol) {
                                if (!wouldBeInCheck(row, col, row + direction, newCol)) {
                                    moves.push({
                                        row: row + direction,
                                        col: newCol,
                                        isEnPassant: true
                                    });
                                }
                            }
                        }
                    }
                    break;

                case 'r': // Rook
                    for (const [dr, dc] of [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1]
                        ]) {
                        let newRow = row + dr;
                        let newCol = col + dc;
                        while (isValidSquare(newRow, newCol)) {
                            if (!boardState[newRow][newCol]) {
                                if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                    moves.push({
                                        row: newRow,
                                        col: newCol
                                    });
                                }
                            } else {
                                if ((isUpperCase(boardState[newRow][newCol]) !== isUpperCase(piece))) {
                                    if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                        moves.push({
                                            row: newRow,
                                            col: newCol
                                        });
                                    }
                                }
                                break;
                            }
                            newRow += dr;
                            newCol += dc;
                        }
                    }
                    break;

                case 'n': // Knight
                    for (const [dr, dc] of [
                            [2, 1],
                            [2, -1],
                            [-2, 1],
                            [-2, -1],
                            [1, 2],
                            [1, -2],
                            [-1, 2],
                            [-1, -2]
                        ]) {
                        const newRow = row + dr;
                        const newCol = col + dc;
                        if (isValidSquare(newRow, newCol) &&
                            (!boardState[newRow][newCol] ||
                                (isUpperCase(boardState[newRow][newCol]) !== isUpperCase(piece)))) {
                            if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                moves.push({
                                    row: newRow,
                                    col: newCol
                                });
                            }
                        }
                    }
                    break;

                case 'b': // Bishop
                    for (const [dr, dc] of [
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ]) {
                        let newRow = row + dr;
                        let newCol = col + dc;
                        while (isValidSquare(newRow, newCol)) {
                            if (!boardState[newRow][newCol]) {
                                if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                    moves.push({
                                        row: newRow,
                                        col: newCol
                                    });
                                }
                            } else {
                                if ((isUpperCase(boardState[newRow][newCol]) !== isUpperCase(piece))) {
                                    if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                        moves.push({
                                            row: newRow,
                                            col: newCol
                                        });
                                    }
                                }
                                break;
                            }
                            newRow += dr;
                            newCol += dc;
                        }
                    }
                    break;

                case 'q': // Queen
                    // Combine rook and bishop moves
                    for (const [dr, dc] of [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1],
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ]) {
                        let newRow = row + dr;
                        let newCol = col + dc;
                        while (isValidSquare(newRow, newCol)) {
                            if (!boardState[newRow][newCol]) {
                                if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                    moves.push({
                                        row: newRow,
                                        col: newCol
                                    });
                                }
                            } else {
                                if ((isUpperCase(boardState[newRow][newCol]) !== isUpperCase(piece))) {
                                    if (!wouldBeInCheck(row, col, newRow, newCol)) {
                                        moves.push({
                                            row: newRow,
                                            col: newCol
                                        });
                                    }
                                }
                                break;
                            }
                            newRow += dr;
                            newCol += dc;
                        }
                    }
                    break;

                case 'k': // King
                    for (const [dr, dc] of [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1],
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ]) {
                        const newRow = row + dr;
                        const newCol = col + dc;
                        if (isValidSquare(newRow, newCol) &&
                            (!boardState[newRow][newCol] ||
                                (isUpperCase(boardState[newRow][newCol]) !== isUpperCase(piece)))) {
                            if (!isSquareUnderAttack(newRow, newCol, enemyColor)) {
                                moves.push({
                                    row: newRow,
                                    col: newCol
                                });
                            }
                        }
                    }

                    // Castling
                    if (color === 'white') {
                        // Kingside
                        if (castlingRights.white.kingSide &&
                            !boardState[7][5] && !boardState[7][6] &&
                            !isSquareUnderAttack(7, 4, 'black') &&
                            !isSquareUnderAttack(7, 5, 'black') &&
                            !isSquareUnderAttack(7, 6, 'black')) {
                            moves.push({
                                row: 7,
                                col: 6,
                                isCastle: true
                            });
                        }
                        // Queenside
                        if (castlingRights.white.queenSide &&
                            !boardState[7][3] && !boardState[7][2] && !boardState[7][1] &&
                            !isSquareUnderAttack(7, 4, 'black') &&
                            !isSquareUnderAttack(7, 3, 'black') &&
                            !isSquareUnderAttack(7, 2, 'black')) {
                            moves.push({
                                row: 7,
                                col: 2,
                                isCastle: true
                            });
                        }
                    } else {
                        // Kingside
                        if (castlingRights.black.kingSide &&
                            !boardState[0][5] && !boardState[0][6] &&
                            !isSquareUnderAttack(0, 4, 'white') &&
                            !isSquareUnderAttack(0, 5, 'white') &&
                            !isSquareUnderAttack(0, 6, 'white')) {
                            moves.push({
                                row: 0,
                                col: 6,
                                isCastle: true
                            });
                        }
                        // Queenside
                        if (castlingRights.black.queenSide &&
                            !boardState[0][3] && !boardState[0][2] && !boardState[0][1] &&
                            !isSquareUnderAttack(0, 4, 'white') &&
                            !isSquareUnderAttack(0, 3, 'white') &&
                            !isSquareUnderAttack(0, 2, 'white')) {
                            moves.push({
                                row: 0,
                                col: 2,
                                isCastle: true
                            });
                        }
                    }
                    break;
            }

            return moves;
        }

        // Make a move on the board
        function makeMove(fromRow, fromCol, toRow, toCol) {
            const piece = boardState[fromRow][fromCol];
            const move = possibleMoves.find(m => m.row === toRow && m.col === toCol);

            // Handle en passant
            if (move?.isEnPassant) {
                boardState[toRow + (currentPlayer === 'white' ? 1 : -1)][toCol] = '';
            }

            // Handle castling
            if (move?.isCastle) {
                if (toCol === 6) { // Kingside
                    boardState[toRow][5] = boardState[toRow][7];
                    boardState[toRow][7] = '';
                } else if (toCol === 2) { // Queenside
                    boardState[toRow][3] = boardState[toRow][0];
                    boardState[toRow][0] = '';
                }
            }

            // Update en passant target
            enPassantTarget = null;
            if (piece.toLowerCase() === 'p' && Math.abs(fromRow - toRow) === 2) {
                enPassantTarget = {
                    row: (fromRow + toRow) / 2,
                    col: fromCol
                };
            }

            // Update castling rights
            if (piece.toLowerCase() === 'k') {
                if (currentPlayer === 'white') {
                    castlingRights.white.kingSide = false;
                    castlingRights.white.queenSide = false;
                } else {
                    castlingRights.black.kingSide = false;
                    castlingRights.black.queenSide = false;
                }
            } else if (piece.toLowerCase() === 'r') {
                if (currentPlayer === 'white') {
                    if (fromRow === 7 && fromCol === 0) castlingRights.white.queenSide = false;
                    if (fromRow === 7 && fromCol === 7) castlingRights.white.kingSide = false;
                } else {
                    if (fromRow === 0 && fromCol === 0) castlingRights.black.queenSide = false;
                    if (fromRow === 0 && fromCol === 7) castlingRights.black.kingSide = false;
                }
            }

            // Handle pawn promotion
            if (piece.toLowerCase() === 'p' && (toRow === 0 || toRow === 7)) {
                boardState[toRow][toCol] = currentPlayer === 'white' ? 'Q' : 'q';
            } else {
                boardState[toRow][toCol] = piece;
            }

            boardState[fromRow][fromCol] = '';

            // Update king position
            if (piece.toLowerCase() === 'k') {
                if (currentPlayer === 'white') {
                    whiteKingPos = {
                        row: toRow,
                        col: toCol
                    };
                } else {
                    blackKingPos = {
                        row: toRow,
                        col: toCol
                    };
                }
            }
        }

        // Check if a square is under attack
        function isSquareUnderAttack(row, col, byColor) {
            for (let r = 0; r < 8; r++) {
                for (let c = 0; c < 8; c++) {
                    const piece = boardState[r][c];
                    if (piece && ((byColor === 'white' && isUpperCase(piece)) ||
                            (byColor === 'black' && !isUpperCase(piece)))) {
                        const moves = getRawMoves(r, c, true);
                        if (moves.some(move => move.row === row && move.col === col)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        // Get moves without checking for check (for attack detection)
        function getRawMoves(row, col, attackOnly = false) {
            const piece = boardState[row][col];
            if (!piece) return [];

            const moves = [];
            const color = isUpperCase(piece) ? 'white' : 'black';
            const pieceType = piece.toLowerCase();

            switch (pieceType) {
                case 'p': // Pawn
                    const direction = color === 'white' ? -1 : 1;

                    if (!attackOnly) {
                        // Forward move
                        if (isValidSquare(row + direction, col) && !boardState[row + direction][col]) {
                            moves.push({
                                row: row + direction,
                                col
                            });

                            // Double move from starting position
                            const startRow = color === 'white' ? 6 : 1;
                            if (row === startRow && !boardState[row + 2 * direction][col]) {
                                moves.push({
                                    row: row + 2 * direction,
                                    col
                                });
                            }
                        }
                    }

                    // Captures
                    for (const dc of [-1, 1]) {
                        const newCol = col + dc;
                        if (newCol >= 0 && newCol < 8) {
                            if (isValidSquare(row + direction, newCol)) {
                                moves.push({
                                    row: row + direction,
                                    col: newCol
                                });
                            }
                        }
                    }
                    break;

                case 'r': // Rook
                case 'b': // Bishop
                case 'q': // Queen
                    const directions = pieceType === 'r' ? [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1]
                        ] :
                        pieceType === 'b' ? [
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ] : [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1],
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ];

                    for (const [dr, dc] of directions) {
                        let newRow = row + dr;
                        let newCol = col + dc;
                        while (isValidSquare(newRow, newCol)) {
                            moves.push({
                                row: newRow,
                                col: newCol
                            });
                            if (boardState[newRow][newCol]) break;
                            newRow += dr;
                            newCol += dc;
                        }
                    }
                    break;

                case 'n': // Knight
                    for (const [dr, dc] of [
                            [2, 1],
                            [2, -1],
                            [-2, 1],
                            [-2, -1],
                            [1, 2],
                            [1, -2],
                            [-1, 2],
                            [-1, -2]
                        ]) {
                        const newRow = row + dr;
                        const newCol = col + dc;
                        if (isValidSquare(newRow, newCol)) {
                            moves.push({
                                row: newRow,
                                col: newCol
                            });
                        }
                    }
                    break;

                case 'k': // King
                    for (const [dr, dc] of [
                            [1, 0],
                            [-1, 0],
                            [0, 1],
                            [0, -1],
                            [1, 1],
                            [1, -1],
                            [-1, 1],
                            [-1, -1]
                        ]) {
                        const newRow = row + dr;
                        const newCol = col + dc;
                        if (isValidSquare(newRow, newCol)) {
                            moves.push({
                                row: newRow,
                                col: newCol
                            });
                        }
                    }
                    break;
            }

            return moves;
        }

        // Check if a move would leave the king in check
        function wouldBeInCheck(fromRow, fromCol, toRow, toCol) {
            const piece = boardState[fromRow][fromCol];
            const capturedPiece = boardState[toRow][toCol];

            // Make the move temporarily
            boardState[toRow][toCol] = piece;
            boardState[fromRow][fromCol] = '';

            // Check if king is in check
            const kingPos = isUpperCase(piece) ? whiteKingPos : blackKingPos;
            const kingRow = piece.toLowerCase() === 'k' ? toRow : kingPos.row;
            const kingCol = piece.toLowerCase() === 'k' ? toCol : kingPos.col;

            const inCheck = isSquareUnderAttack(kingRow, kingCol, isUpperCase(piece) ? 'black' : 'white');

            // Undo the move
            boardState[fromRow][fromCol] = piece;
            boardState[toRow][toCol] = capturedPiece;

            return inCheck;
        }

        // Check for checkmate
        function isCheckmate() {
            const kingPos = currentPlayer === 'white' ? whiteKingPos : blackKingPos;

            // First check if king is in check
            if (!isSquareUnderAttack(kingPos.row, kingPos.col, currentPlayer === 'white' ? 'black' : 'white')) {
                return false;
            }

            // Check if any piece can move to get out of check
            for (let row = 0; row < 8; row++) {
                for (let col = 0; col < 8; col++) {
                    const piece = boardState[row][col];
                    if (piece && ((currentPlayer === 'white' && isUpperCase(piece)) ||
                            (currentPlayer === 'black' && !isUpperCase(piece)))) {
                        const moves = getPossibleMoves(row, col);
                        if (moves.length > 0) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }

        // Check for stalemate
        function isStalemate() {
            const kingPos = currentPlayer === 'white' ? whiteKingPos : blackKingPos;

            // First check if king is NOT in check
            if (isSquareUnderAttack(kingPos.row, kingPos.col, currentPlayer === 'white' ? 'black' : 'white')) {
                return false;
            }

            // Check if any piece can move
            for (let row = 0; row < 8; row++) {
                for (let col = 0; col < 8; col++) {
                    const piece = boardState[row][col];
                    if (piece && ((currentPlayer === 'white' && isUpperCase(piece)) ||
                            (currentPlayer === 'black' && !isUpperCase(piece)))) {
                        const moves = getPossibleMoves(row, col);
                        if (moves.length > 0) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }

        // Helper functions
        function isValidSquare(row, col) {
            return row >= 0 && row < 8 && col >= 0 && col < 8;
        }

        function isUpperCase(char) {
            return char === char.toUpperCase() && char !== char.toLowerCase();
        }

        function updateStatus() {
            turnIndicator.textContent = currentPlayer === 'white' ? 'White' : 'Black';
            turnIndicator.style.color = currentPlayer === 'white' ? '#333' : '#dc3545';

            if (!gameOver) {
                const kingPos = currentPlayer === 'white' ? whiteKingPos : blackKingPos;
                if (isSquareUnderAttack(kingPos.row, kingPos.col, currentPlayer === 'white' ? 'black' : 'white')) {
                    statusElement.textContent = `${currentPlayer === 'white' ? 'White' : 'Black'} is in check!`;
                } else {
                    statusElement.textContent = `${currentPlayer === 'white' ? 'White' : 'Black'}'s turn`;
                }
            }
        }

        // Reset the game
        function resetGame() {
            boardState = [
                ['r', 'n', 'b', 'q', 'k', 'b', 'n', 'r'],
                ['p', 'p', 'p', 'p', 'p', 'p', 'p', 'p'],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['', '', '', '', '', '', '', ''],
                ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P'],
                ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R']
            ];
            currentPlayer = 'white';
            selectedSquare = null;
            possibleMoves = [];
            gameOver = false;
            whiteKingPos = {
                row: 7,
                col: 4
            };
            blackKingPos = {
                row: 0,
                col: 4
            };
            enPassantTarget = null;
            castlingRights = {
                white: {
                    kingSide: true,
                    queenSide: true
                },
                black: {
                    kingSide: true,
                    queenSide: true
                }
            };
            winMessageElement.textContent = '';
            renderBoard();
        }

        // Event listeners
        resetBtn.addEventListener('click', resetGame);

        // Initialize the game
        renderBoard();
    </script>
</body>

</html>