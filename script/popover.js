document.addEventListener('DOMContentLoaded', function () {
    const popoverTrigger = document.getElementById('contactPopoverBtn');

    if (popoverTrigger) {
        new bootstrap.Popover(popoverTrigger, {
            container: 'body',
            html: true,
            trigger: 'click',
            placement: 'auto',
            content: `
                <div class="popover-contact-content">
                    <p><strong>Phone:</strong> <a href="tel:+639202012915">(+63) 920 2012 915</a></p>
                    <p><strong>Email:</strong> <a href="mailto:FilomanSeman@pinoy.ph">FilomanSeman@pinoy.ph</a></p>
                </div>
            `
        });
    }
});
