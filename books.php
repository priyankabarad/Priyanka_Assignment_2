<?php
require('db_connection_mysqli.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Initialize variables and error messages
$bookName = $bookDescription = $quantityAvailable = $price = $authorName = $publisher = $publicationYear = $genre = $language = "";
$bookNameErr = $bookDescriptionErr = $quantityAvailableErr = $priceErr = $authorNameErr = $publisherErr = $publicationYearErr = $genreErr = $languageErr = "";

$productAddedBy = "Admin";  // Hardcoded as per the requirement

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Book Name
    if (empty($_POST["BookName"])) {
        $bookNameErr = "Book Name is required";
    } else {
        $bookName = cleanInput($_POST["BookName"]);
        if (!preg_match("/^[a-zA-Z0-9 ]*$/", $bookName)) {
            $bookNameErr = "Only letters, numbers, and white space allowed";
        }
    }

    // Validate Book Description
    if (empty($_POST["BookDescription"])) {
        $bookDescriptionErr = "Book Description is required";
    } else {
        $bookDescription = cleanInput($_POST["BookDescription"]);
    }

    // Validate Quantity Available (must be a positive integer)
    if (empty($_POST["QuantityAvailable"])) {
        $quantityAvailableErr = "Quantity is required";
    } else {
        $quantityAvailable = cleanInput($_POST["QuantityAvailable"]);
        if (!filter_var($quantityAvailable, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $quantityAvailableErr = "Quantity must be a positive integer";
        }
    }

    // Validate Price (must be a positive number)
    if (empty($_POST["Price"])) {
        $priceErr = "Price is required";
    } else {
        $price = cleanInput($_POST["Price"]);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
            $priceErr = "Price must be a positive number";
        }
    }

    // Validate Author Name
    if (empty($_POST["AuthorName"])) {
        $authorNameErr = "Author Name is required";
    } else {
        $authorName = cleanInput($_POST["AuthorName"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $authorName)) {
            $authorNameErr = "Only letters and white space allowed";
        }
    }

    // Validate Publisher
    if (empty($_POST["Publisher"])) {
        $publisherErr = "Publisher is required";
    } else {
        $publisher = cleanInput($_POST["Publisher"]);
    }

    // Validate Publication Year
    if (empty($_POST["PublicationYear"])) {
        $publicationYearErr = "Publication Year is required";
    } else {
        $publicationYear = cleanInput($_POST["PublicationYear"]);
        if (!filter_var($publicationYear, FILTER_VALIDATE_INT) || $publicationYear < 0) {
            $publicationYearErr = "Invalid publication year";
        }
    }

    // Validate Genre
    if (empty($_POST["Genre"])) {
        $genreErr = "Genre is required";
    } else {
        $genre = cleanInput($_POST["Genre"]);
    }

    // Validate Language
    if (empty($_POST["Language"])) {
        $languageErr = "Language is required";
    } else {
        $language = cleanInput($_POST["Language"]);
    }

    // If no errors, insert the data into the database
    if (empty($bookNameErr) && empty($bookDescriptionErr) && empty($quantityAvailableErr) && empty($priceErr) && empty($authorNameErr) && empty($publisherErr) && empty($publicationYearErr) && empty($genreErr) && empty($languageErr)) {
        $stmt = $dbc->prepare("INSERT INTO books (BookName, BookDescription, QuantityAvailable, Price, AuthorName, Publisher, PublicationYear, Genre, Language, ProductAddedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssssss", $bookName, $bookDescription, $quantityAvailable, $price, $authorName, $publisher, $publicationYear, $genre, $language, $productAddedBy);

        if ($stmt->execute()) {
            // Redirect to the index page after successful insert
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
        }
        .table {
            background-color: white;
            color: black;
        }
        .footer {
            background-color: #6a11cb;
            padding: 10px 0;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #6a11cb;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Book Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Manage Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php">Add Book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Add New Book</h2>
        <form method="POST" action="books.php">
            <div class="mb-3">
                <label for="BookName" class="form-label">Book Name</label>
                <input type="text" class="form-control" id="BookName" name="BookName" value="<?php echo $bookName; ?>">
                <span class="text-danger"><?php echo $bookNameErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="BookDescription" class="form-label">Book Description</label>
                <input type="text" class="form-control" id="BookDescription" name="BookDescription" value="<?php echo $bookDescription; ?>">
                <span class="text-danger"><?php echo $bookDescriptionErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="QuantityAvailable" class="form-label">Quantity Available</label>
                <input type="number" class="form-control" id="QuantityAvailable" name="QuantityAvailable" value="<?php echo $quantityAvailable; ?>">
                <span class="text-danger"><?php echo $quantityAvailableErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="Price" class="form-label">Price</label>
                <input type="number" class="form-control" id="Price" name="Price" value="<?php echo $price; ?>" step="0.01">
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="AuthorName" class="form-label">Author Name</label>
                <input type="text" class="form-control" id="AuthorName" name="AuthorName" value="<?php echo $authorName; ?>">
                <span class="text-danger"><?php echo $authorNameErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="Publisher" class="form-label">Publisher</label>
                <input type="text" class="form-control" id="Publisher" name="Publisher" value="<?php echo $publisher; ?>">
                <span class="text-danger"><?php echo $publisherErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="PublicationYear" class="form-label">Publication Year</label>
                <input type="number" class="form-control" id="PublicationYear" name="PublicationYear" value="<?php echo $publicationYear; ?>">
                <span class="text-danger"><?php echo $publicationYearErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="Genre" class="form-label">Genre</label>
                <select class="form-select" id="Genre" name="Genre" value="<?php echo $genre; ?>">
                    <option value="" disabled selected>Select Genre</option>
                    <option value="Fiction">Fiction</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                    <option value="Science Fiction">Science Fiction</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Biography">Biography</option>
                    <option value="Self-Help">Self-Help</option>
                </select>
                <span class="text-danger"><?php echo $genreErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="Language" class="form-label">Language</label>
                <select class="form-select" id="Language" name="Language" value="<?php echo $language; ?>">
                    <option value="" disabled selected>Select Language</option>
                    <option value="English">English</option>
                    <option value="Spanish">Spanish</option>
                    <option value="French">French</option>
                    <option value="German">German</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Japanese">Japanese</option>
                </select>
                <span class="text-danger"><?php echo $languageErr; ?></span>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <footer class="footer mt-auto py-3">
        <div class="container">
        © 2024 Book Store. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
