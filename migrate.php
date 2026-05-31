<?php
require 'includes/db.php';

try {
    // 1. Create events table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `events` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `venue` VARCHAR(255) NOT NULL,
            `event_date` VARCHAR(150) NOT NULL,
            `is_active` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Check if there is already an event, if not, insert the default one
    $stmt = $pdo->query("SELECT COUNT(*) FROM events");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $pdo->exec("
            INSERT INTO events (title, venue, event_date, is_active) 
            VALUES (
                'DNSC INSTITUTE OF COMPUTING INDUSTRY ADVISORY COUNCIL MEETING through SPRINT-IT',
                'DNSC GAD Conference Room and Via MS Teams',
                'May 4, 2026 8:00-1:00 PM',
                1
            )
        ");
    }

    // 3. Add event_id to covenant_submissions if it doesn't exist
    $checkColumn = $pdo->query("SHOW COLUMNS FROM `covenant_submissions` LIKE 'event_id'");
    if ($checkColumn->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `covenant_submissions` ADD `event_id` INT(11) DEFAULT 1 AFTER `id`");
        
        // Try adding foreign key constraint
        $pdo->exec("ALTER TABLE `covenant_submissions` ADD CONSTRAINT `fk_event_submission` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE SET NULL");
    }

    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
