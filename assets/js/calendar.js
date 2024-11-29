//date month selector
function showDropdown() {
    document.getElementById('month-year-dropdown').style.display = 'block';
}

function hideDropdown() {
    document.getElementById('month-year-dropdown').style.display = 'none';
}

function updateCalendar() {
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;
    console.log(`Selected month: ${month}, year: ${year}`);
    window.location.href = `?month=${month}&year=${year}`;
}




//POP UP module
function openPopup(date) {
    document.getElementById('task_date').value = date; // Set the selected date
    document.getElementById('popupForm').style.display = 'flex'; // Show popup
}

function closePopup() {
    document.getElementById('popupForm').style.display = 'none'; // Hide popup
}

// Handle form submission
document.getElementById('taskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('save_task.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Task saved successfully!');
            closePopup();
            location.reload(); // Reload calendar
        } else {
            alert('Error saving task: ' + data.error);
        }
    });
});





