<?php $services = $data['services'] ?? []; ?>
<h2>Book Now</h2>
<div class="book-wrap">
<div id="messageBox" style="display:none;"></div>
<form id="bookingForm" method="post" action="<?php echo $base_url; ?>?action=book">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="phone">Phone Number:</label>
    <input type="tel" name="phone" id="phone" title="Please enter a valid 10-digit phone number">

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="address">Address:</label>
    <input type="text" name="address" id="address" required>

    <label for="appointmentDate">Appointment Date:</label>
    <input type="date" name="appointmentDate" id="appointmentDate" required>

    <label for="appointmentTime">Appointment Time:</label>
    <select name="appointmentTime" id="appointmentTime" required>
        <!-- Options populated by JavaScript -->
    </select>

    <section>
        <h3>Choose a Package</h3>
        <?php foreach ($services as $service): ?>
            <label for="<?php echo htmlspecialchars(strtolower($service['service_name'])); ?>">
                <input type="radio" id="<?php echo htmlspecialchars(strtolower($service['service_name'])); ?>" name="package" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                <?php echo htmlspecialchars($service['service_name']); ?> - $<?php echo number_format($service['price'], 2); ?>
            </label>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
            <p>No packages available at this time.</p>
        <?php endif; ?>

        <p id="packageDuration"><strong>Duration:</strong> Please select a package.</p>
        <input type="hidden" name="duration" id="hiddenDuration">
    </section>

    <input type="hidden" name="book_appointment" value="1">
    <button type="submit">Book Appointment</button>
</form>
</div>

<script>
    // Show message if present
    <?php if (isset($data['error'])): ?>
        document.addEventListener("DOMContentLoaded", function() {
            var messageBox = document.getElementById("messageBox");
            messageBox.style.display = "block";
            messageBox.style.backgroundColor = "#f8d7da";
            messageBox.style.color = "#721c24";
            messageBox.textContent = "<?php echo htmlspecialchars($data['error']); ?>";
            setTimeout(() => {
                messageBox.style.display = "none";
            }, 5000);
        });
    <?php elseif (isset($data['success'])): ?>
        document.addEventListener("DOMContentLoaded", function() {
            var messageBox = document.getElementById("messageBox");
            messageBox.style.display = "block";
            messageBox.style.backgroundColor = "#d4edda";
            messageBox.style.color = "#155724";
            messageBox.textContent = "<?php echo htmlspecialchars($data['success']); ?>";
            document.getElementById("bookingForm").reset();
            document.getElementById("packageDuration").textContent = "Duration: Please select a package.";
            setTimeout(() => {
                messageBox.style.display = "none";
            }, 5000);
        });
    <?php endif; ?>

    // Phone number formatting
    document.getElementById("phone").addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length > 10) value = value.slice(0, 10);
        let formattedValue = value;
        if (value.length > 6) {
            formattedValue = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
        } else if (value.length > 3) {
            formattedValue = `(${value.slice(0, 3)}) ${value.slice(3)}`;
        }
        e.target.value = formattedValue;
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Date setup
        var todayDate = new Date();
        todayDate.setHours(0, 0, 0, 0); // Normalize to midnight
        var today = todayDate.toISOString().split("T")[0]; // e.g., "2025-04-13"
        console.log("Computed today:", today);
        var maxDateDate = new Date(todayDate.getFullYear() + 1, todayDate.getMonth(), todayDate.getDate());
        var maxDate = maxDateDate.toISOString().split("T")[0];

        var dateInput = document.getElementById("appointmentDate");
        dateInput.setAttribute("min", today);
        dateInput.setAttribute("max", maxDate);

        // Time options
        var timeSelect = document.getElementById("appointmentTime");
        var startHour = 8;
        var endHour = 17;
        var intervalTimes = [];

        // Generate time slots (8:00 AM to 5:30 PM)
        for (var hour = startHour; hour < endHour; hour++) {
            var ampm = hour < 12 ? "AM" : "PM";
            var hour12 = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
            intervalTimes.push(hour12.toString().padStart(2, '0') + ":00 " + ampm);
            intervalTimes.push(hour12.toString().padStart(2, '0') + ":30 " + ampm);
        }

        // Populate dropdown
        function updateTimeOptions() {
            timeSelect.innerHTML = ''; // Clear existing options
            var selectedDate = dateInput.value; // e.g., "2025-04-14"
            console.log("Selected Date:", selectedDate);
            var selectedDateObj = selectedDate ? new Date(selectedDate) : null;
            selectedDateObj && selectedDateObj.setHours(0, 0, 0, 0);
            var isToday = selectedDateObj && selectedDateObj.getTime() === todayDate.getTime();

            intervalTimes.forEach(function(time) {
                var option = document.createElement("option");
                option.value = time;
                option.text = time;
                if (isToday) {
                    var timeParts = time.match(/(\d+):(\d+)\s*(AM|PM)/);
                    if (timeParts) {
                        var hours = parseInt(timeParts[1]);
                        var minutes = parseInt(timeParts[2]);
                        if (timeParts[3] === "PM" && hours !== 12) hours += 12;
                        if (timeParts[3] === "AM" && hours === 12) hours = 0;
                        var optionTime = new Date(todayDate.getFullYear(), todayDate.getMonth(), todayDate.getDate(), hours, minutes);
                        if (optionTime < todayDate) {
                            option.disabled = true;
                        }
                    }
                }
                timeSelect.appendChild(option);
            });
            console.log("Time options updated for date:", selectedDate || "none", "isToday:", isToday, "selectedDate:", selectedDate);
        }

        // Initial population of time slots on page load
        updateTimeOptions();

        // Update times when date changes
        dateInput.addEventListener("change", updateTimeOptions);
    });
</script>