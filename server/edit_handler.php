<?php
require '../server/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['id'];
    $newDate = $_POST['appointment_date'];
    $newMechanicId = $_POST['mechanic_id'];

    // Check if the appointment ID is valid
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);

    if (!$stmt->fetch()) {
        die("Invalid appointment ID.");
    }

    // Check new mechanic availability
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

    // Update the appointment
    $stmt = $conn->prepare("
        UPDATE appointments
        SET appointment_date = ?, mechanic_id = ?
        WHERE id = ?
    ");
    $stmt->execute([$newDate, $newMechanicId, $appointmentId]);

    echo "Appointment updated successfully!";
    header("Location: ../public/admin.php");
    exit;
}
?>
