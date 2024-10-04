<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Fetch existing books from the database
$query = "SELECT * FROM books";
$result = mysqli_query($dbc, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- Font Awesome CSS -->
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
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #6a11cb;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Book Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Manage Books</a>
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
        <h2 class="text-center">Book Products</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Description</th>
                    <th>Quantity Available</th>
                    <th>Price</th>
                    <th>Author</th>
                    <th>Publisher</th>
                    <th>Publication Year</th>
                    <th>Genre</th>
                    <th>Language</th>
                    <th>Added By</th>
                    <th>Actions</th> <!-- Edit and Delete buttons -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Display each book in a table row
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['BookName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['BookDescription']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['QuantityAvailable']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AuthorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Publisher']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PublicationYear']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Genre']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Language']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProductAddedBy']) . "</td>";
                        echo "<td>
                                <a href='edit.php?BookID=" . $row['BookID'] . "' class='btn btn-warning btn-sm'><i class='fas fa-pencil-alt'></i></a>
                                <a href='delete.php?delete_id=" . $row['BookID'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash-alt'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No books found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3">
        <div class="container">
        © 2024 Book Store. All rights reserved.
           
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
