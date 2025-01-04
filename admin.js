// Dropdown Toggle on Click
const userIcon = document.getElementById('user-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

userIcon.addEventListener('click', function (event) {
    event.stopPropagation(); // Prevent the click from bubbling up to the window
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
});

// Close dropdown if clicked outside
window.addEventListener('click', function () {
    dropdownMenu.style.display = "none";
});

// Document Popup Functionality
document.addEventListener('DOMContentLoaded', () => {
    const triggers = document.querySelectorAll('.popup-trigger');
    const popup = document.querySelector('.popup-box');
    const overlay = document.querySelector('.overlay');
    const popupContent = popup.querySelector('.popup-content');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const filePath = trigger.getAttribute('data-image');
            const fileExtension = filePath.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                // Display images in the popup
                popupContent.innerHTML = `<img src="${filePath}" alt="Document" style="max-width: 100%; height: auto;">`;
            } else if (fileExtension === 'pdf') {
                // Display PDFs in the popup
                popupContent.innerHTML = `<iframe src="${filePath}" style="width: 100%; height: 500px;" frameborder="0"></iframe>`;
            } else {
                // Unsupported format message
                popupContent.innerHTML = `<p>Unsupported file format.</p>`;
            }

            popup.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    // Close popup when clicking on overlay
    overlay.addEventListener('click', closePopup);

    // Close popup with close button
    const closeButton = document.querySelector('.popup-box .close-btn');
    closeButton.addEventListener('click', closePopup);

    function closePopup() {
        popup.style.display = 'none';
        overlay.style.display = 'none';
        popupContent.innerHTML = ''; // Clear content for next use
    }
});

// Handle Accept and Reject Buttons
document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('table');

    // Delegate event listeners to buttons
    table.addEventListener('click', (event) => {
        const target = event.target;
        const row = target.closest('tr');
        const id = row.dataset.id;

        if (target.classList.contains('tick')) {
            updateStatus(id, 'accept', row);
        }

        if (target.classList.contains('cross')) {
            updateStatus(id, 'reject', row);
        }
    });

    function updateStatus(id, action, row) {
        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, action }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusCell = row.querySelector('td:last-child');
                    if (action === 'accept') {
                        statusCell.innerHTML = `<span class="status accepted">Accepted</span>`;
                    } else if (action === 'reject') {
                        statusCell.innerHTML = `<span class="status rejected">Rejected</span>`;
                    }
                } else {
                    alert(data.message || "Error updating status.");
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
