<?php
require 'db.php';

if ($_GET['action'] === 'getMechanics') {
    $stmt = $conn->query("SELECT * FROM mechanics");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($_GET['action'] === 'bookAppointment') {
    $clientData = $_POST;
    $mechanicId = $clientData['mechanic_id'];
    $appointmentDate = $clientData['appointment_date'];

    // Check if the client already has an appointment on this date
    $stmt = $conn->prepare("
        SELECT * FROM appointments
        WHERE client_id = ? AND appointment_date = ?
    ");
    $stmt->execute([$clientData['id'], $appointmentDate]);
    if ($stmt->rowCount() > 0) {
        echo "You already have an appointment on this date.";
        exit;
    }

    // Check mechanic availability for the selected date
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS booked_slots
        FROM appointments
        WHERE mechanic_id = ? AND appointment_date = ?
    ");
    $stmt->execute([$mechanicId, $appointmentDate]);
    $result = $stmt->fetch();

    if ($result['booked_slots'] >= 4) {
        echo "Selected mechanic is fully booked for this day.";
        exit;
    }

    // Insert client and appointment
    $stmt = $conn->prepare("INSERT INTO clients (name, address, phone, car_license, car_engine) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$clientData['name'], $clientData['address'], $clientData['phone'], $clientData['car_license'], $clientData['car_engine']]);
    $clientId = $conn->lastInsertId();

    $stmt = $conn->prepare("INSERT INTO appointments (client_id, mechanic_id, appointment_date) VALUES (?, ?, ?)");
    $stmt->execute([$clientId, $mechanicId, $appointmentDate]);

    echo "Appointment booked successfully!";
}

?>