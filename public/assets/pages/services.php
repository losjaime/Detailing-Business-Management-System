<h2>Services</h2>
<p><b><a href="<?php echo $base_url; ?>?action=book">Book</a> your appointment with us today!</b></p>
<p><b>Prices may vary depending on vehicle size and condition.</b></p>
<div id="flow">
    <?php if (!empty($data['services'])): ?>
        <?php foreach ($data['services'] as $service): ?>
            <section>
                <h3><?php echo htmlspecialchars($service['service_name']); ?> - $<?php echo number_format($service['price'], 2); ?></h3>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($service['duration']); ?> minutes</p>
                <ul>
                    <?php foreach (explode(", ", $service['description']) as $item): ?>
                        <li><?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No services are currently available. Please check back later.</p>
    <?php endif; ?>
</div>
