<h2>Our Services</h2>
<p class="section-sub">Prices may vary depending on vehicle size and condition.</p>

<div id="flow">
    <?php if (!empty($data['services'])): ?>
        <?php foreach ($data['services'] as $service): ?>
            <section class="service-card">
                <div class="service-header">
                    <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                    <span class="service-price">$<?php echo number_format($service['price'], 2); ?></span>
                </div>
                <span class="duration-badge">⏱ <?php echo htmlspecialchars($service['duration']); ?> min</span>
                <ul>
                    <?php foreach (explode(", ", $service['description']) as $item): ?>
                        <li><?php echo htmlspecialchars(trim($item)); ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo $base_url; ?>?action=book" class="btn btn-primary btn-sm">Book This Package</a>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No services are currently available. Please check back later.</p>
    <?php endif; ?>
</div>
