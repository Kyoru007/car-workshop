<?php
require '../server/db.php';

// Fetch appointments data
$stmt = $conn->query("
    SELECT a.id, c.name AS client_name, c.phone, c.car_license, a.appointment_date, m.name AS mechanic_name
    FROM appointments a
    JOIN clients c ON a.client_id = c.id
    JOIN mechanics m ON a.mechanic_id = m.id
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Panel</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <h1>Admin Panel</h1>
        <table id="appointments">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Car License</th>
                    <th>Appointment Date</th>
                    <th>Mechanic</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?= htmlspecialchars($appt['client_name']) ?></td>
                            <td><?= htmlspecialchars($appt['phone']) ?></td>
                            <td><?= htmlspecialchars($appt['car_license']) ?></td>
                            <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($appt['mechanic_name']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $appt['id'] ?>" class="btn btn-primary">Edit</a>
                                <a href="../server/admin_handler.php?action=deleteAppointment&id=<?= $appt['id'] ?>"
                                    onclick="return confirm('Are you sure you want to delete this appointment?')"
                                    class="btn btn-danger">Delete</a>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>

</html>