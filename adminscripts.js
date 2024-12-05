function toggleTable(tableId) {
    const table = document.getElementById(tableId);
    if (table.style.display === "none" || table.style.display === "") {
        table.style.display = "table";
    } else {
        table.style.display = "none";
    }
}
document.addEventListener("DOMContentLoaded", () => {
    // Initially hide all tables
    document.querySelectorAll('.room-table').forEach(table => {
        table.style.display = "none";
    });
});
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [
            // Example events
            {
                title: 'Conference Room A Booking',
                start: '2024-12-01',
                end: '2024-12-02'
            },
            {
                title: 'Meeting Room B Booking',
                start: '2024-12-05'
            }
        ]
    });

    calendar.render();
});
