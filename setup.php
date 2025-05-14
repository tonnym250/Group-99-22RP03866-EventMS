<?php
// setup.php - Script to set up the database and initial data

require_once 'config.php';
require_once 'database.php';

// Display header
echo "=================================================\n";
echo "USSD Event Registration System - Database Setup\n";
echo "=================================================\n\n";

try {
    // Initialize the database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "Connected to database successfully.\n";
    
    // Initialize tables
    echo "Creating database tables...\n";
    $db->initializeTables();
    echo "Database tables created successfully.\n";
    
    // Insert sample events
    echo "Inserting sample events...\n";
    $db->insertSampleEvents();
    echo "Sample events inserted successfully.\n";
    
    echo "\nSetup completed successfully!\n";
    echo "You can now run the USSD application.\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Setup failed. Please check your database configuration in config.php.\n";
}