<?php
require '../server/db.php';

if (isset($_GET['id'])) {
    $appointmentId = $_GET['id'];

    // Fetch appointment details
    $stmt = $conn->prepare("
        SELECT a.id, c.name AS client_name, a.appointment_date, a.mechanic_id
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        WHERE a.id = ?
    ");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        die("Invalid appointment ID or no appointment found.");
    }

    // Fetch mechanics for the dropdown
    $stmt = $conn->query("SELECT * FROM mechanics");
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("No appointment ID provided.");
}
?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Appointment</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <h1>Edit Appointment</h1>
        <form action="../server/edit_handler.php" method="POST">
            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">

            <label for="client-name">Client Name:</label>
            <input type="text" id="client-name" value="<?= htmlspecialchars($appointment['client_name']) ?>" readonly>

            <label for="appointment-date">Appointment Date:</label>
            <input type="date" id="appointment-date" name="appointment_date"
                value="<?= $appointment['appointment_date'] ?>" required>

            <label for="mechanic-id">Mechanic:</label>
            <select id="mechanic-id" name="mechanic_id">
                <?php foreach ($mechanics as $mechanic): ?>
                    <option value="<?= $mechanic['id'] ?>" <?= $mechanic['id'] == $appointment['mechanic_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mechanic['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </body>

</html>