<?php
function renderStars(int $rating): string {
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}
?>

<h2>Customer Reviews</h2>
<p class="section-sub">See what our clients have to say — and leave your own review below.</p>

<?php if (!empty($data['success'])): ?>
    <div class="form-msg msg-success"><?php echo htmlspecialchars($data['success']); ?></div>
<?php elseif (!empty($data['error'])): ?>
    <div class="form-msg msg-error"><?php echo htmlspecialchars($data['error']); ?></div>
<?php endif; ?>

<!-- Approved reviews -->
<?php if (!empty($data['testimonials'])): ?>
    <div class="testimonial-grid">
        <?php foreach ($data['testimonials'] as $t): ?>
            <div class="testimonial-card">
                <div class="testimonial-stars"><?php echo renderStars((int)$t['rating']); ?></div>
                <p class="testimonial-message">"<?php echo htmlspecialchars($t['message']); ?>"</p>
                <div class="testimonial-footer">
                    <span class="testimonial-name">— <?php echo htmlspecialchars($t['client_name']); ?></span>
                    <span class="testimonial-date"><?php echo date('M j, Y', strtotime($t['created_at'])); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-reviews">
        <p>No reviews yet — be the first to leave one!</p>
    </div>
<?php endif; ?>

<!-- Submission form -->
<div class="testimonial-form-wrap">
    <h3>Leave a Review</h3>
    <form method="post" action="<?php echo $base_url; ?>?action=testimonials" class="testimonial-form">

        <label for="client_name">Your Name</label>
        <input type="text" id="client_name" name="client_name"
               placeholder="e.g. John Smith" required
               value="<?php echo htmlspecialchars($_POST['client_name'] ?? ''); ?>">

        <label>Rating</label>
        <div class="star-picker" id="starPicker">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" name="rating" id="star<?php echo $i; ?>"
                       value="<?php echo $i; ?>"
                       <?php echo (isset($_POST['rating']) && (int)$_POST['rating'] === $i) ? 'checked' : ''; ?>>
                <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?>">★</label>
            <?php endfor; ?>
        </div>

        <label for="message">Your Review</label>
        <textarea id="message" name="message" rows="4"
                  placeholder="Tell us about your experience..."
                  required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>

        <button type="submit" name="submit_testimonial" class="btn btn-primary">Submit Review</button>
    </form>
</div>
