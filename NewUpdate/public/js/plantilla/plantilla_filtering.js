$(document).ready(function() {
    // Function to filter the table based on selected filters
    function filterTable() {
        var officeFilter = $('#officeFilter').val().toLowerCase();  // Get the selected office filter value
        var statusFilter = $('#statusFilter').val().toLowerCase();  // Get the selected status filter value

        // Loop through each row in the table and hide or show based on filter values
        $('#employeeTable tr').each(function() {
            var rowOffice = $(this).data('office') ? $(this).data('office').toLowerCase() : '';
            var rowStatus = $(this).data('status') ? $(this).data('status').toLowerCase() : '';

            // If the row matches the filters, show it, otherwise hide it
            if (
                (officeFilter === '' || rowOffice.indexOf(officeFilter) !== -1) &&
                (statusFilter === '' || rowStatus.indexOf(statusFilter) !== -1)
            ) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Event listener for filter button
    $('#filterBtn').on('click', function() {
        filterTable(); // Trigger filter when the button is clicked
    });

    // Optional: You can keep this if you want to reset the filters or add some other functionality
    // $('#officeFilter, #statusFilter').on('change', function() {
    //     // No action here, as we only want the filter to apply on button click
    // });
});

document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        document.querySelector(".skeleton-loader").style.display = "none";  
        document.getElementById("personnelTableContainer").style.display = "block";
    }, 500); // Adjust delay if needed
});

document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add("loaded"); 
});
