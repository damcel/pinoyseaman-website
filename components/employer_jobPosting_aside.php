<div class="currency-date-aside">
    <aside class="currency-container">
        <div class="highlight-box">
            <h5>Post highlights</h5>
            <p class="subtext">In the last 30 days</p>
            <img class="highlight-img" src="https://img.icons8.com/office/80/laptop.png" alt="No highlights" />
            <p class="highlight-empty">No highlights</p>
            <p class="highlight-sub">No recent post to highlight.</p>
            </div>
    </aside>
    <aside class="job-post-container"> 
        <h2 class="job-post-h2">Recent Job Posted</h2>
        <?php if (count($jobs) > 0): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-item">
                    <div class="job-information">
                        <p class="employer-post-job-title"><?= htmlspecialchars($job['job_title']) ?></p>
                        <button 
                            class="job-edit-icon edit-job-btn" 
                            aria-label="Edit <?= htmlspecialchars($job['job_title']) ?>" 
                            data-bs-toggle="modal" 
                            data-bs-target="#edit-recent-job"
                            data-job-code="<?= htmlspecialchars($job['code']) ?>"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>
                    <div class="job-meta">
                        <time class="job-date">
                            <?= !empty($job['date_posted']) ? date('d M Y', strtotime($job['date_posted'])) : '' ?>
                        </time>
                        <div class="job-status">
                            <?= (!empty($job['expiry']) && strtotime($job['expiry']) < time()) ? 'Completed' : 'Active' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No recent job postings found.</p>
        <?php endif; ?>
    </aside>

    <aside class="calendar-container">
        <!-- Footer Section -->
        <footer class="page-footer">
            <ul class="footer-links">
            <li>About us</li>
            <li>Our Story</li>
            <li>Privacy & Terms</li>
            <li>Advertise</li>
            <li>Ad Choices</li>
            <li>Get in Touch</li>
            </ul>
            <div class="footer-branding">
                <img src="pinoyseaman-logo/alternativeHeaderLogo.png" alt="alternative-logo">
                <p>
                    pinoyseaman.com © 2025
                </p>
            </div>
        </footer>
    </aside>

</div>