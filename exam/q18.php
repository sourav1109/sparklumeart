<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .book-info {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .book-info h2 {
            margin: 0 0 10px 0;
        }
        .search-box {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Library System</h1>

    <div class="search-box">
        <form method="POST">
            <label for="search">Search Book by Title or Author:</label>
            <input type="text" name="search" id="search" placeholder="Enter title or author">
            <input type="submit" value="Search">
        </form>
    </div>

    <?php
    // Define the Book class
    class Book {
        public $title;
        public $author;
        public $isbn;

        public function __construct($title, $author, $isbn) {
            $this->title = $title;
            $this->author = $author;
            $this->isbn = $isbn;
        }

        public function displayInfo() {
            return "<b>Title:</b> {$this->title} <br><b>Author:</b> {$this->author} <br><b>ISBN:</b> {$this->isbn}<br><br>";
        }
    }

    // Define the Library class
    class Library {
        private $books = [];

        // Add a book to the library
        public function addBook($title, $author, $isbn) {
            $this->books[] = new Book($title, $author, $isbn);
        }

        // Display all books
        public function displayBooks() {
            if (empty($this->books)) {
                echo "<p>No books available in the library.</p>";
                return;
            }

            echo "<div class='book-info'>";
            echo "<h2>Library Books:</h2>";

            foreach ($this->books as $book) {
                echo $book->displayInfo();
            }

            echo "</div>";
        }

        // Search for a book by title or author
        public function searchBooks($searchTerm) {
            $results = [];
            foreach ($this->books as $book) {
                if (strpos(strtolower($book->title), strtolower($searchTerm)) !== false || strpos(strtolower($book->author), strtolower($searchTerm)) !== false) {
                    $results[] = $book;
                }
            }

            return $results;
        }
    }

    // Create a Library instance
    $library = new Library();

    // Add some books to the library
    $library->addBook("To Kill a Mockingbird", "Harper Lee", "9780061120084");
    $library->addBook("1984", "George Orwell", "9780451524935");
    $library->addBook("Pride and Prejudice", "Jane Austen", "9780141040349");

    // Display all books in the library
    $library->displayBooks();

    // Handle search form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
        $searchTerm = trim($_POST["search"]);
        if ($searchTerm) {
            $results = $library->searchBooks($searchTerm);

            if (count($results) > 0) {
                echo "<div class='book-info'>";
                echo "<h2>Search Results:</h2>";
                foreach ($results as $book) {
                    echo $book->displayInfo();
                }
                echo "</div>";
            } else {
                echo "<p>No books found matching '$searchTerm'.</p>";
            }
        }
    }
    ?>

</body>
</html>
