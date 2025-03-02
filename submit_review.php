<?php
// Database connection
$host = 'localhost';
$dbname = 'car_dealership';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $reviewer_name = $_POST['reviewer_name'];
    $review_content = $_POST['review_content'];
    $review_rating = $_POST['review_rating'];

    // Insert review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (reviewer_name, review_content, review_rating) VALUES (:reviewer_name, :review_content, :review_rating)");
    $stmt->bindParam(':reviewer_name', $reviewer_name);
    $stmt->bindParam(':review_content', $review_content);
    $stmt->bindParam(':review_rating', $review_rating);
    $stmt->execute();

    // Redirect back to the testimonial page
    header("Location: testimonial.html");
    exit();
} catch (PDOException $e) {
    echo "Error submitting review: " . $e->getMessage();
}
?>