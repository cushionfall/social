import random
import os
import json
random.seed()

def draw_board(board):
    """Display the current state of board."""
    for row in board:
        print(" | ".join(row))
        print("-" * 9)

def welcome(board):
    """Print a welcome message and show board."""       
    print("Welcome to the classic Noughts and Crosses game!")
    draw_board(board)

def initialise_board(board):
    """Resets the board with empty spaces."""
    for i in range(3):
        for j in range(3):
            board[i][j] = " "
    return board

def get_player_move(board):
    """Prompt the player to enter a valid move and return the coordinates."""
    while True:
        try:
            move = int(input("Choose a position (1-9): ")) - 1
            row, col = divmod(move, 3)
            if board[row][col] == ' ':
                return row, col
            else:
                print("Position already taken, try again.")
        except (ValueError, IndexError):
            print("Invalid input. Please enter a number between 1 and 9.")

def check_for_win(board, mark):
    """Check if the given mark has won the game."""
    for row in board:
        if row[0] == row[1] == row[2] == mark:
            return True
    for col in range(3):
        if board[0][col] == board[1][col] == board[2][col] == mark:
            return True
    if board[0][0] == board[1][1] == board[2][2] == mark:
        return True
    if board[0][2] == board[1][1] == board[2][0] == mark:
        return True
    return False

def check_for_draw(board):
    """Determine if the game is a draw (when no spaces are left)."""
    for row in board:
        for cell in row:
            if cell == " ":
                return False
    return True

def minimax(board, is_maximizing):
    """Recursive Minimax algorithm to choose the best move."""
    if check_for_win(board, 'O'):
        return 1
    elif check_for_win(board, 'X'):
        return -1
    elif check_for_draw(board):
        return 0

    if is_maximizing:
        best_score = -float('inf')
        for i in range(3):
            for j in range(3):
                if board[i][j] == ' ':
                    board[i][j] = 'O'
                    score = minimax(board, False)
                    board[i][j] = ' '
                    best_score = max(score, best_score)
        return best_score
    else:
        best_score = float('inf')
        for i in range(3):
            for j in range(3):
                if board[i][j] == ' ':
                    board[i][j] = 'X'
                    score = minimax(board, True)
                    board[i][j] = ' '
                    best_score = min(score, best_score)
        return best_score

def choose_computer_move(board):
    """Use Minimax to choose the best move for the computer."""
    best_score = -float('inf')
    best_move = None
    for i in range(3):
        for j in range(3):
            if board[i][j] == ' ':
                board[i][j] = 'O'
                score = minimax(board, False)
                board[i][j] = ' '
                if score > best_score:
                    best_score = score
                    best_move = (i, j)
    return best_move

def play_game(board):
    """Run the main game loop, alternating between player and computer."""
    board = initialise_board(board)
    draw_board(board)
    while True:
        row, col = get_player_move(board)
        board[row][col] = 'X'
        draw_board(board)
        if check_for_win(board, 'X'):
            print("You win!")
            return 1
        if check_for_draw(board):
            print("It's a tie!")
            return 0
        
        print("Computer's turn...")
        row, col = choose_computer_move(board)
        board[row][col] = 'O'
        draw_board(board)
        if check_for_win(board, 'O'):
            print("Computer wins!")
            return -1
        if check_for_draw(board):
            print("It's a tie!")
            return 0

def menu():
    """Show menu options and return the user's choice."""
    print("\n1 - Start game")
    print("2 - Save score")
    print("3 - Load leaderboard")
    print("q - Quit game")
    return input("Enter choice: ")

def load_scores():
    """Read leaderboard data from file and return as dictionary, handling empty file errors."""
    if os.path.exists("leaderboard.txt"):
        try:
            with open("leaderboard.txt", "r") as file:
                data = file.read()
                return json.loads(data) if data else {}
        except (json.JSONDecodeError, FileNotFoundError):
            return {}
    return {}

def save_score(score):
    """Ask for player's name and record their score in the leaderboard file."""
    name = input("Enter your name: ")
    scores = load_scores()
    scores[name] = scores.get(name, 0) + score
    with open("leaderboard.txt", "w") as file:
        json.dump(scores, file)
    print("Score successfully saved!")

def display_leaderboard(leaders):
    """Show the leaderboard in order of highest score."""
    print("Leaderboard:")
    for name, score in sorted(leaders.items(), key=lambda x: x[1], reverse=True):
        print(f"{name}: {score}")

# --- Main loop ---
if __name__ == "__main__":
    board = [[" "]*3 for _ in range(3)]
    welcome(board)
    while True:
        choice = menu()
        if choice == "1":
            result = play_game(board)
        elif choice == "2":
            if 'result' in locals():
                save_score(result)
            else:
                print("You need to play a game first!")
        elif choice == "3":
            leaders = load_scores()
            display_leaderboard(leaders)
        elif choice.lower() == "q":
            print("Thanks for playing!")
            break
        else:
            print("Invalid choice. Please try again.")
