<?php
require 'db.php';
$sql = "ALTER TABLE ga4_history_2025 
    ADD COLUMN IF NOT EXISTS mobile_users INT DEFAULT 0, 
    ADD COLUMN IF NOT EXISTS tablet_users INT DEFAULT 0, 
    ADD COLUMN IF NOT EXISTS desktop_users INT DEFAULT 0, 
    ADD COLUMN IF NOT EXISTS avg_engagement_time FLOAT DEFAULT 0";
$res = $conn->query($sql);
echo $res ? "OK: SCHEMA_UPDATED" : "ERROR: " . $conn->error;
