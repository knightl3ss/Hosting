document.addEventListener('DOMContentLoaded', function () {
    // Store the current filter criteria
    var currentFilters = {
        remarks: '',
        office: '',
        status: ''
    };

    // Handle filter button click
    document.getElementById('filterBtn').addEventListener('click', function () {
        const officeFilter = document.getElementById('officeFilter').value;
        const remarksFilter = document.getElementById('remarksFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;

        console.log('Filters:', {
            office: officeFilter,
            remarks: remarksFilter,
            status: statusFilter
        });

        currentFilters = { remarks: remarksFilter, office: officeFilter, status: statusFilter };

        // Send the filters to the server to get the filtered data
        fetchFilteredData(currentFilters);
    });

    // Function to send the filters and fetch filtered data from the server
    function fetchFilteredData(filters) {
        fetch(filterUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(filters),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Filtered Data:', data); // Debugging log

            const tableBody = document.getElementById('employeeTable');
            tableBody.innerHTML = '';  // Clear current table rows

            if (data.personnels && data.personnels.length > 0) {
                // Populate the table with the filtered data
                data.personnels.forEach(personnel => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${personnel.office}</td>
                        <td>${personnel.itemNo}</td>
                        <td>${personnel.position}</td>
                        <td>${personnel.salaryGrade}</td>
                        <td>${personnel.authorizedSalary}</td>
                        <td>${personnel.actualSalary}</td>
                        <td>${personnel.step}</td>
                        <td>${personnel.code}</td>
                        <td>${personnel.type}</td>
                        <td>${personnel.level}</td>
                        <td>${personnel.lastName}</td>
                        <td>${personnel.firstName}</td>
                        <td>${personnel.middleName ?? ''}</td>
                        <td>${personnel.dob}</td>
                        <td>${personnel.originalAppointment}</td>
                        <td>${personnel.lastPromotion ?? 'N/A'}</td>
                        <td>${personnel.status}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="17" class="text-center">No personnel found.</td>`;
                tableBody.appendChild(row);
            }
        })
        .catch(error => {
            console.error('Error fetching filtered data:', error);
        });
    }

    // jQuery-based filter logic for table on client-side
    // We removed the automatic trigger for the remarks filter

    $('#remarksFilter, #officeFilter, #statusFilter').on('change', function () {
        // No automatic trigger for filter changes, will only apply on clicking the filter button
    });

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
