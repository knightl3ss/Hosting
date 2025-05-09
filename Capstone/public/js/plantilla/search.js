$(document).ready(function() {
    // Get the search input element
    const searchInput = $('#searchInput');

    // Get the table body
    const tableBody = $('#employeeTable');

    // Event listener for input in the search bar
    searchInput.on('input', function() {
        // Get the value entered in the search bar
        const searchTerm = searchInput.val().toLowerCase();

        // Loop through all rows in the table
        tableBody.find('tr').each(function() {
            // Get all the text content of the current row
            const rowText = $(this).text().toLowerCase();

            // Check if the row contains the search term
            if (rowText.indexOf(searchTerm) !== -1) {
                // Show the row if it matches the search term
                $(this).show();
            } else {
                // Hide the row if it does not match the search term
                $(this).hide();
            }
        });
    });
});
