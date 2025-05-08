$(document).ready(function() {
    // Store the current filter criteria
    var currentFilters = {
        office: '',
        status: ''
    };

    // Function to trigger the filter when the user changes any filter
    $('#officeFilter, #statusFilter').on('change', function() {
        var office = $('#officeFilter').val();    // Get selected office value
        var status = $('#statusFilter').val();    // Get selected status value

        // Update the current filter values
        currentFilters = { office, status };

        // If office or status filter is applied, show the filtered table
        if (office || status) {
            $('#originalPersonnelTableContainer').show();
            filterPersonnelData(office, status);
        } else {
            // If no filter is applied, show the original table
            $('#originalPersonnelTableContainer').show();
        }
    });

    // Function to filter personnel data based on criteria
    function filterPersonnelData(office, status) {
        var filteredPersonnel = [];

        // Loop through all personnel data and filter based on the provided criteria
        $('.employee-data').each(function() {
            var personnel = $(this);

            // Ensure office and status are part of the data (data-office and data-status attributes)
            var matchesOffice = !office || personnel.data('office') === office;
            var matchesStatus = !status || personnel.data('status') === status;

            // Check if all the filter conditions are met
            if (matchesOffice && matchesStatus) {
                var dob = personnel.find('td').eq(13).text();  // Get the date of birth
                var retirementDate = getRetirementDate(dob);  // Calculate the retirement date

                // Push the matched personnel into the filtered array
                filteredPersonnel.push({
                    office: personnel.find('td').eq(0).text(),
                    position: personnel.find('td').eq(2).text(),
                    lastName: personnel.find('td').eq(10).text(),
                    firstName: personnel.find('td').eq(11).text(),
                    middleName: personnel.find('td').eq(12).text(),
                    dob: dob,
                    retirementDate: retirementDate,  // Store dynamic retirement date
                    status: personnel.find('td').eq(15).text() // Status column
                });
            }
        });

        // Clear previous filtered data from the table body
        // $('#filteredEmployeeTableBody').empty();

        // If there is any filtered personnel, display them in the filtered table
        // if (filteredPersonnel.length > 0) {
        //     filteredPersonnel.forEach(function(personnel) {
        //         var row = `<tr>
        //                      <td>${personnel.office}</td>
        //                      <td>${personnel.position}</td>
        //                      <td>${personnel.lastName}</td>
        //                      <td>${personnel.firstName}</td>
        //                      <td>${personnel.middleName}</td>
        //                      <td>${getAge(personnel.dob)}</td>
        //                      <td>${personnel.retirementDate}</td>
        //                      </tr>`;
        //         $('#filteredEmployeeTableBody').append(row);
        //     });
        // } else {
        //     $('#filteredEmployeeTableBody').append('<tr><td colspan="7" class="text-center">No personnel found.</td></tr>');
        // }
    }

    // Helper function to calculate retirement date based on birthdate
    // function getRetirementDate(dob) {
    //     var birthDate = new Date(dob);
    //     var retirementYear = birthDate.getFullYear() + 60;  // Add 60 years to the birth year

    //     // Set the retirement date to the person's 60th birthday (same day/month)
    //     var retirementDate = new Date(retirementYear, birthDate.getMonth(), birthDate.getDate());

    //     // Return formatted date as string (e.g., "June 30, 2050")
    //     return retirementDate.toLocaleDateString();  // You can customize this format
    // }

    // Helper function to calculate age from birthdate
    // function getAge(birthDate) {
    //     var birth = new Date(birthDate);
    //     var today = new Date();
    //     var age = today.getFullYear() - birth.getFullYear();
    //     var m = today.getMonth() - birth.getMonth();
    //     if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    //         age--;
    //     }
    //     return age;
    // }
});
