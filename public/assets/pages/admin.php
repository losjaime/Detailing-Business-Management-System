<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url);
    exit;
}
?>

<h2>Admin Dashboard</h2>
<?php if (!empty($data['message'])): ?>
    <p style="color: <?php echo strpos($data['message'], 'Error') === false ? 'green' : 'red'; ?>;">
        <?php echo htmlspecialchars($data['message']); ?>
    </p>
<?php endif; ?>

<h3>Clients</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Action</th>
    </tr>
    <?php foreach ($data['clients'] as $client): ?>
        <tr>
            <td><?php echo htmlspecialchars($client['client_id']); ?></td>
            <td><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></td>
            <td><?php echo htmlspecialchars($client['email']); ?></td>
            <td><?php echo htmlspecialchars($client['phone']); ?></td>
            <td><?php echo htmlspecialchars($client['address']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                    <input type="text" name="new_email" placeholder="Update Email">
                    <button type="submit" name="update_client">Update</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                    <button type="submit" name="delete_client" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Add Client</h3>
<form method="post">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="text" name="address" placeholder="Address" required>
    <button type="submit" name="add_client">Add Client</button>
</form>

<h3>Services</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Duration (mins)</th>
        <th>Action</th>
    </tr>
    <?php foreach ($data['services'] as $service): ?>
        <tr>
            <td><?php echo htmlspecialchars($service['service_id']); ?></td>
            <td><?php echo htmlspecialchars($service['service_name']); ?></td>
            <td><?php echo htmlspecialchars($service['description']); ?></td>
            <td><?php echo htmlspecialchars($service['price']); ?></td>
            <td><?php echo htmlspecialchars($service['duration']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                    <input type="text" name="new_price" placeholder="Update Price">
                    <input type="text" name="new_duration" placeholder="Update Duration">
                    <button type="submit" name="update_service">Update</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                    <button type="submit" name="delete_service" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Add Service</h3>
<form method="post">
    <input type="text" name="service_name" placeholder="Service Name" required>
    <input type="text" name="description" placeholder="Description" required>
    <input type="number" name="price" placeholder="Price" step="0.01" required>
    <input type="number" name="duration" placeholder="Duration (minutes)" required>
    <button type="submit" name="add_service">Add Service</button>
</form>

<h3>Appointments</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Service</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($data['appointments'] as $appointment): ?>
        <tr>
            <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
            <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
            <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                    <select name="new_status">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <button type="submit" name="update_appointment">Update</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                    <button type="submit" name="delete_appointment" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php //include 'templates/footer.php'; 
?>