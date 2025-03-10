document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search_table');
    const table = document.getElementById('main_table');
    const tableBody = table.querySelector('tbody');

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let found = false;

            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(filter)) {
                    found = true;
                }
            });

            row.style.display = found ? '' : 'none';
        });
    });
});