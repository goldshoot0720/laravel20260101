<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Information</h1>";
echo "<h2>PHP Version: " . PHP_VERSION . "</h2>";
echo "<h2>Current Directory: " . __DIR__ . "</h2>";
echo "<h2>Files in directory:</h2>";
echo "<pre>";
print_r(scandir(__DIR__));
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo "<h2>Database Test:</h2>";
try {
    require_once 'config/database.php';
    echo "Database connection: " . (testDatabaseConnection() ? "SUCCESS" : "FAILED") . "<br>";
    echo "Environment: " . DB_ENVIRONMENT . "<br>";
    echo "Database: " . DB_DATABASE . "<br>";
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}
?>