document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const trackProgressBtn = document.getElementById('trackProgressBtn');
    const searchTicketModalElement = document.getElementById('searchTicketModal');
    const loginModalElement = document.getElementById('loginModal');

    // Initialize modals only if elements exist
    let loginModal, searchTicketModal;
    
    if (loginModalElement) {
        loginModal = new bootstrap.Modal(loginModalElement);
    }
    
    if (searchTicketModalElement) {
        searchTicketModal = new bootstrap.Modal(searchTicketModalElement);
    }

    // Track Progress button click handler
    trackProgressBtn.addEventListener('click', function() {
        // Get URLs from data attributes
        const dashboardUrl = trackProgressBtn.dataset.dashboardUrl;
        const historyUrl = trackProgressBtn.dataset.historyUrl;
        
        // If user is logged in (has dashboard link), go directly to history page
        if (document.querySelector('a[href="' + dashboardUrl + '"]')) {
            window.location.href = historyUrl;
            return;
        }

        // For non-logged in users, show search ticket modal
        if (searchTicketModal) {
            searchTicketModal.show();
        }
    });

    // Handle form submission
    const searchTicketForm = document.getElementById('searchTicketForm');
    if (searchTicketForm) {
        searchTicketForm.addEventListener('submit', function(e) {
            const complaintId = searchTicketForm.querySelector('#complaint_id').value;
            if (!complaintId) {
                e.preventDefault();
                alert('Please enter a complaint ID');
                return;
            }
        });
    }
});
