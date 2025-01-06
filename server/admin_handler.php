<?php
require 'db.php';

if ($_GET['action'] === 'getAppointments') {
    $stmt = $conn->query("
        SELECT a.id, c.name AS client_name, c.phone, c.car_license, a.appointment_date, m.name AS mechanic_name
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        JOIN mechanics m ON a.mechanic_id = m.id
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($_GET['action'] === 'updateAppointment') {
    $appointmentId = $_POST['id'];
    $newDate = $_POST['appointment_date'];
    $newMechanicId = $_POST['mechanic_id'];

    // Check new mechanic availability for the selected date
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS booked_slots
        FROM appointments
        WHERE mechanic_id = ? AND appointment_date = ?
    ");
    $stmt->execute([$newMechanicId, $newDate]);
    $result = $stmt->fetch();

    if ($result['booked_slots'] >= 4) {
        echo "The selected mechanic is fully booked for this date.";
        exit;
    }

    // Update the appointment
    $stmt = $conn->prepare("
        UPDATE appointments
        SET appointment_date = ?, mechanic_id = ?
        WHERE id = ?
    ");
    $stmt->execute([$newDate, $newMechanicId, $appointmentId]);

    echo "Appointment updated successfully!";
}

if ($_GET['action'] === 'deleteAppointment') {
    $appointmentId = $_GET['id'];

    // Delete the appointment
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);

    // Redirect back to the admin panel
    header("Location: ../public/admin.php");
    exit;
}


?>