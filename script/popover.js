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
                    <p><strong>Phone:</strong> <a id="applicantPhone"></a></p>
                    <p><strong>Email:</strong> <a id="applicantEmail"><i class="fa-solid fa-envelope"></i></a></p>
                </div>
            `
        });
    }
});
