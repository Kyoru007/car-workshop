<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['id'];
    $newDate = $_POST['appointment_date'];
    $newMechanicId = $_POST['mechanic_id'];

    // Check if the selected mechanic is available for the given date
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS booked_slots
        FROM appointments
        WHERE mechanic_id = ? AND appointment_date = ?
    ");
    $stmt->execute([$newMechanicId, $newDate]);
    $result = $stmt->fetch();

    if ($result['booked_slots'] >= 4) {
        die("The selected mechanic is fully booked for this date.");
    }

    // Update the appointment in the database
    $stmt = $conn->prepare("
        UPDATE appointments
        SET appointment_date = ?, mechanic_id = ?
        WHERE id = ?
    ");
    $stmt->execute([$newDate, $newMechanicId, $appointmentId]);

    // Redirect back to the admin panel
    header("Location: ../public/admin.html");
    exit;
}
?>