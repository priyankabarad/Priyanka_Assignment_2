<?php
require('db_connection_mysqli.php');

// Initialize variables to hold form values and error messages
$bookId = $bookName = $bookDescription = $quantityAvailable = $price = $authorName = $publisher = $publicationYear = $genre = $language = "";
$bookNameErr = $bookDescriptionErr = $quantityAvailableErr = $priceErr = $authorNameErr = $publisherErr = $publicationYearErr = $genreErr = $languageErr = "";

// Check if the book ID is provided
if (isset($_GET['BookID'])) {
    $bookId = $_GET['BookID'];

    // Fetch existing book details from the database
    $query = "SELECT * FROM books WHERE BookID = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If the book is found, populate the form fields
    if ($row = mysqli_fetch_assoc($result)) {
        $bookName = $row['BookName'];
        $bookDescription = $row['BookDescription'];
        $quantityAvailable = $row['QuantityAvailable'];
        $price = $row['Price'];
        $authorName = $row['AuthorName'];
        $publisher = $row['Publisher'];
        $publicationYear = $row['PublicationYear'];
        $genre = $row['Genre'];
        $language = $row['Language'];
    } else {
        echo "Book not found!";
        exit;
    }
}

// Function to sanitize form inputs
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate form inputs after submission
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
        $quantityAvailableErr = "Quantity Available is required";
    } else {
        $quantityAvailable = cleanInput($_POST["QuantityAvailable"]);
        if (!filter_var($quantityAvailable, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $quantityAvailableErr = "Quantity Available must be a positive integer";
        }
    }

    // Validate Price (must be a positive decimal number)
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

    // If all validations pass, proceed with form submission
    if (empty($bookNameErr) && empty($bookDescriptionErr) && empty($quantityAvailableErr) && empty($priceErr) &&
        empty($authorNameErr) && empty($publisherErr) && empty($publicationYearErr) && empty($genreErr) && 
        empty($languageErr)) {

        // Clean inputs
        $bookName_clean = prepare_string($dbc, $bookName);
        $bookDescription_clean = prepare_string($dbc, $bookDescription);
        $quantityAvailable_clean = prepare_string($dbc, $quantityAvailable);
        $price_clean = prepare_string($dbc, $price);
        $authorName_clean = prepare_string($dbc, $authorName);
        $publisher_clean = prepare_string($dbc, $publisher);
        $publicationYear_clean = prepare_string($dbc, $publicationYear);
        $genre_clean = prepare_string($dbc, $genre);
        $language_clean = prepare_string($dbc, $language);

        // Update data in the database
        $updateQuery = "UPDATE books SET BookName=?, BookDescription=?, QuantityAvailable=?, Price=?, AuthorName=?, Publisher=?, PublicationYear=?, Genre=?, Language=? WHERE BookID=?";
        $updateStmt = mysqli_prepare($dbc, $updateQuery);

        // Bind parameters
        mysqli_stmt_bind_param($updateStmt, 'sssssssssi', $bookName_clean, $bookDescription_clean, $quantityAvailable_clean, 
                               $price_clean, $authorName_clean, $publisher_clean, $publicationYear_clean, $genre_clean, $language_clean, $bookId);

        // Execute the statement
        $result = mysqli_stmt_execute($updateStmt);

        if ($result) {
            header("Location: index.php"); // Redirect on success to refresh the page
            exit;
        } else {
            echo "<br>Some error in updating the data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Book</h2>
        <form action="edit.php?BookID=<?php echo $bookId; ?>" method="POST">
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
                <input type="number" class="form-control" id="Price" name="Price" step="0.01" value="<?php echo $price; ?>">
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
                <input type="text" class="form-control" id="PublicationYear" name="PublicationYear" value="<?php echo $publicationYear; ?>">
                <span class="text-danger"><?php echo $publicationYearErr; ?></span>
            </div>
            <div class="mb-3">
        <label for="Genre" class="form-label">Genre</label>
        <select class="form-select" id="Genre" name="Genre">
            <option value="" disabled>Select Genre</option>
            <option value="Fiction" <?php if ($genre == "Fiction") echo 'selected'; ?>>Fiction</option>
            <option value="Non-Fiction" <?php if ($genre == "Non-Fiction") echo 'selected'; ?>>Non-Fiction</option>
            <option value="Science Fiction" <?php if ($genre == "Science Fiction") echo 'selected'; ?>>Science Fiction</option>
            <option value="Fantasy" <?php if ($genre == "Fantasy") echo 'selected'; ?>>Fantasy</option>
            <option value="Biography" <?php if ($genre == "Biography") echo 'selected'; ?>>Biography</option>
            <option value="Self-Help" <?php if ($genre == "Self-Help") echo 'selected'; ?>>Self-Help</option>
        </select>
        <span class="text-danger"><?php echo $genreErr; ?></span>
    </div>

    <div class="mb-3">
        <label for="Language" class="form-label">Language</label>
        <select class="form-select" id="Language" name="Language">
            <option value="" disabled>Select Language</option>
            <option value="English" <?php if ($language == "English") echo 'selected'; ?>>English</option>
            <option value="Spanish" <?php if ($language == "Spanish") echo 'selected'; ?>>Spanish</option>
            <option value="French" <?php if ($language == "French") echo 'selected'; ?>>French</option>
            <option value="German" <?php if ($language == "German") echo 'selected'; ?>>German</option>
            <option value="Chinese" <?php if ($language == "Chinese") echo 'selected'; ?>>Chinese</option>
            <option value="Japanese" <?php if ($language == "Japanese") echo 'selected'; ?>>Japanese</option>
        </select>
        <span class="text-danger"><?php echo $languageErr; ?></span>
    </div>

            <button type="submit" class="btn btn-primary">Update Book</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
