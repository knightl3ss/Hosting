$(document).ready(function () {
    // Store the current filter criteria
    var currentFilters = {
        remarks: '',
        office: '',
        status: ''
    };

    // jQuery-based filter logic for table on client-side
    $('#remarksFilter, #officeFilter, #statusFilter').on('change', function () {
        var remarks = $('#remarksFilter').val();  // Get selected remarks value
        var office = $('#officeFilter').val();    // Get selected office value
        var status = $('#statusFilter').val();    // Get selected status value

        // Update the current filter values
        currentFilters = { remarks, office, status };

        // Apply filter based on client-side data
        if (remarks) {
            $('#originalPersonnelTableContainer').hide();
            $('#filteredPersonnelTableContainer').show();
            filterPersonnelData(remarks, office, status);
        } else if (office || status) {
            // If no remarks filter but other filters applied, show the original table
            $('#originalPersonnelTableContainer').show();
            $('#filteredPersonnelTableContainer').hide();
        } else {
            // If no filters applied, show the original table and hide the filtered table
            $('#originalPersonnelTableContainer').show();
            $('#filteredPersonnelTableContainer').hide();
        }
    });

    // Function to filter personnel data based on criteria (for client-side filtering)
    function filterPersonnelData(remarks, office, status) {
        var filteredPersonnel = [];

        // Loop through all personnel data and filter based on all provided criteria
        $('.employee-data').each(function () {
            var personnel = $(this);

            var matchesRemarks = !remarks || personnel.data('remarks') === remarks;
            var matchesOffice = !office || personnel.data('office') === office;
            var matchesStatus = !status || personnel.data('status') === status;

            // Check if all the filter conditions are met
            if (matchesRemarks && matchesOffice && matchesStatus) {
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
                    retirementDate: retirementDate,
                    remarks: personnel.find('td').eq(7).text() // Remarks column
                });
            }
        });

        // Clear previous filtered data from the table body
        $('#filteredEmployeeTableBody').empty();

        // If there is any filtered personnel, display them in the filtered table
        if (filteredPersonnel.length > 0) {
            filteredPersonnel.forEach(function (personnel) {
                var row = `<tr>
                             <td>${personnel.office}</td>
                             <td>${personnel.position}</td>
                             <td>${personnel.lastName}</td>
                             <td>${personnel.firstName}</td>
                             <td>${personnel.middleName}</td>
                             <td>${getAge(personnel.dob)}</td>
                             <td>${personnel.retirementDate}</td>
                             </tr>`;
                $('#filteredEmployeeTableBody').append(row);
            });
        } else {
            $('#filteredEmployeeTableBody').append('<tr><td colspan="8" class="text-center">No personnel found.</td></tr>');
        }
    }

    // Helper function to calculate retirement date based on birthdate
    function getRetirementDate(dob) {
        var birthDate = new Date(dob);
        var retirementYear = birthDate.getFullYear() + 60;  // Add 60 years to the birth year
        var retirementDate = new Date(retirementYear, birthDate.getMonth(), birthDate.getDate());
        return retirementDate.toLocaleDateString();  // Customize date format if needed
    }

    // Helper function to calculate age from birthdate
    function getAge(birthDate) {
        var birth = new Date(birthDate);
        var today = new Date();
        var age = today.getFullYear() - birth.getFullYear();
        var m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    }
});
