<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: ' . $base_url);
    exit;
}

$report  = $data['report']          ?? [];
$monthly = $data['monthly_revenue'] ?? [];
$upcoming = $data['upcoming']       ?? [];
$statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
?>

<h2>Admin Dashboard</h2>

<?php if (!empty($data['message'])): ?>
    <p class="admin-msg <?php echo strpos($data['message'], 'Error') === false ? 'msg-success' : 'msg-error'; ?>">
        <?php echo htmlspecialchars($data['message']); ?>
    </p>
<?php endif; ?>

<!-- ── REPORTS ─────────────────────────────────────────── -->
<div class="admin-section">
    <h3>📊 Overview</h3>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-value green">$<?php echo number_format($report['total_revenue'] ?? 0, 2); ?></span>
            <span class="stat-label">Total Revenue</span>
        </div>
        <div class="stat-card">
            <span class="stat-value blue"><?php echo (int)($report['total_appointments'] ?? 0); ?></span>
            <span class="stat-label">Total Appts</span>
        </div>
        <div class="stat-card">
            <span class="stat-value yellow"><?php echo (int)($report['pending_count'] ?? 0); ?></span>
            <span class="stat-label">Pending</span>
        </div>
        <div class="stat-card">
            <span class="stat-value green"><?php echo (int)($report['confirmed_count'] ?? 0); ?></span>
            <span class="stat-label">Confirmed</span>
        </div>
        <div class="stat-card">
            <span class="stat-value blue"><?php echo (int)($report['completed_count'] ?? 0); ?></span>
            <span class="stat-label">Completed</span>
        </div>
        <div class="stat-card">
            <span class="stat-value red"><?php echo (int)($report['cancelled_count'] ?? 0); ?></span>
            <span class="stat-label">Cancelled</span>
        </div>
    </div>

    <?php if (!empty($upcoming)): ?>
        <h4>Upcoming Appointments</h4>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['service_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($u['appointment_date']))); ?></td>
                            <td>$<?php echo number_format($u['price'], 2); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($u['status']); ?>"><?php echo $u['status']; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color:var(--text-muted);font-size:0.9em;margin-top:0.5em;">No upcoming appointments.</p>
    <?php endif; ?>

    <?php if (!empty($monthly)): ?>
        <h4>Revenue by Month (Completed)</h4>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Appointments</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly as $m): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m['month_label']); ?></td>
                            <td><?php echo (int)$m['appointment_count']; ?></td>
                            <td>$<?php echo number_format($m['revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ── APPOINTMENTS ───────────────────────────────────── -->
<div class="admin-section">
    <h3>📅 Appointments</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['appointments'] as $appt): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appt['appointment_id']); ?></td>
                        <td><?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($appt['appointment_date']))); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($appt['status']); ?>">
                                <?php echo htmlspecialchars($appt['status']); ?>
                            </span>
                        </td>
                        <td class="action-cell">
                            <form method="post" class="inline-form">
                                <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>">
                                <select name="new_status">
                                    <?php foreach ($statuses as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo $appt['status'] === $s ? 'selected' : ''; ?>>
                                            <?php echo $s; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_appointment" class="btn btn-blue btn-sm">Save</button>
                            </form>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>">
                                <button type="submit" name="delete_appointment" class="btn btn-red btn-sm"
                                        onclick="return confirm('Delete this appointment?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── REVIEWS ─────────────────────────────────────────── -->
<?php
$t_statuses = ['Pending', 'Approved', 'Rejected'];
function adminStars(int $r): string { return str_repeat('★', $r) . str_repeat('☆', 5 - $r); }
?>
<div class="admin-section">
    <h3>⭐ Reviews</h3>

    <?php if (!empty($data['testimonials'])): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['testimonials'] as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['client_name']); ?></td>
                            <td class="stars-cell"><?php echo adminStars((int)$t['rating']); ?></td>
                            <td class="review-msg"><?php echo htmlspecialchars($t['message']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($t['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($t['status']); ?>">
                                    <?php echo $t['status']; ?>
                                </span>
                            </td>
                            <td class="action-cell">
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="testimonial_id" value="<?php echo $t['testimonial_id']; ?>">
                                    <select name="new_status">
                                        <?php foreach ($t_statuses as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo $t['status'] === $s ? 'selected' : ''; ?>>
                                                <?php echo $s; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_testimonial" class="btn btn-blue btn-sm">Save</button>
                                </form>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="testimonial_id" value="<?php echo $t['testimonial_id']; ?>">
                                    <button type="submit" name="delete_testimonial" class="btn btn-red btn-sm"
                                            onclick="return confirm('Delete this review?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color:var(--text-muted);font-size:0.9em;">No reviews submitted yet.</p>
    <?php endif; ?>
</div>

<!-- ── CLIENTS ────────────────────────────────────────── -->
<div class="admin-section">
    <h3>👤 Clients</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['clients'] as $client): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client['client_id']); ?></td>
                        <td><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                        <td><?php echo htmlspecialchars($client['phone']); ?></td>
                        <td><?php echo htmlspecialchars($client['address']); ?></td>
                        <td class="action-cell">
                            <form method="post" class="inline-form">
                                <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                                <input type="email" name="new_email" placeholder="New email">
                                <button type="submit" name="update_client" class="btn btn-blue btn-sm">Update</button>
                            </form>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                                <button type="submit" name="delete_client" class="btn btn-red btn-sm"
                                        onclick="return confirm('Delete this client?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4>Add Client</h4>
    <form method="post" class="admin-form">
        <input type="text"  name="first_name" placeholder="First Name" required>
        <input type="text"  name="last_name"  placeholder="Last Name"  required>
        <input type="email" name="email"       placeholder="Email"      required>
        <input type="tel"   name="phone"       placeholder="Phone"      required>
        <input type="text"  name="address"     placeholder="Address"    required>
        <button type="submit" name="add_client" class="btn btn-blue">Add Client</button>
    </form>
</div>

<!-- ── SERVICES ───────────────────────────────────────── -->
<div class="admin-section">
    <h3>🔧 Services</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['services'] as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['service_id']); ?></td>
                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($service['description']); ?></td>
                        <td>$<?php echo number_format($service['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($service['duration']); ?> min</td>
                        <td class="action-cell">
                            <form method="post" class="inline-form">
                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                <input type="number" name="new_price"    placeholder="Price"    step="0.01" min="0">
                                <input type="number" name="new_duration" placeholder="Duration" min="1">
                                <button type="submit" name="update_service" class="btn btn-blue btn-sm">Update</button>
                            </form>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                <button type="submit" name="delete_service" class="btn btn-red btn-sm"
                                        onclick="return confirm('Delete this service?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4>Add Service</h4>
    <form method="post" class="admin-form">
        <input type="text"   name="service_name" placeholder="Name"        required>
        <input type="text"   name="description"  placeholder="Description" required>
        <input type="number" name="price"         placeholder="Price"       step="0.01" min="0" required>
        <input type="number" name="duration"      placeholder="Mins"        min="1"     required>
        <button type="submit" name="add_service" class="btn btn-blue">Add Service</button>
    </form>
</div>
