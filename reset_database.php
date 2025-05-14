<?php
// reset_database.php - Script to reset the database and recreate it with Rwanda-specific data

// Include configuration
require_once 'config.php';

try {
    // Connect to database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop existing tables in the correct order (to handle foreign key constraints)
    echo "Dropping existing tables...\n";
    $conn->exec("DROP TABLE IF EXISTS ussd_sessions");
    $conn->exec("DROP TABLE IF EXISTS registrations");
    $conn->exec("DROP TABLE IF EXISTS events");
    
    echo "Tables dropped successfully.\n";
    
    // Include database class to recreate tables and insert sample data
    require_once 'database.php';
    
    // Initialize database
    $database = new Database();
    
    // Create tables
    echo "Creating tables...\n";
    $database->initializeTables();
    
    // Insert sample events with Rwanda-specific venues and prices
    echo "Inserting sample events...\n";
    $database->insertSampleEvents();
    
    echo "Database reset complete! Rwanda-specific venues and prices have been applied.\n";
    
} catch(PDOException $e) {
    die("Database reset failed: " . $e->getMessage());
}
?>
