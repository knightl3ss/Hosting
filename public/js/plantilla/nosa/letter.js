function printNOSA() {
    let printContents = document.querySelector('.card-body').innerHTML; // Get form content
    let nosaModalBody = document.querySelector('.modal-body');

    // Replace form with the letter format inside modal
    nosaModalBody.innerHTML = `
        <div class="nosa-letter">
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; line-height: 1.6; }
                .header-container {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 20px;
                }
                .header-container img {
                    height: 80px; /* Adjust logo size */
                }
                .header-title {
                    flex-grow: 1; /* Pushes title to center */
                    text-align: center;
                }
                h2 { margin: 0; }
                .letter-header, .letter-body, .signature { text-align: left; margin: 20px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .btn-container { text-align: center; margin-top: 20px; }
                .print-btn, .back-btn {
                    padding: 10px 20px; 
                    font-size: 16px; 
                    cursor: pointer;
                    margin: 5px;
                }
            </style>

            <!-- Header with two logos -->
            <div class="header-container">
                <img src="{{ url('images/bitaug.jpg') }}" alt="Bitaug Logo">
                <div class="header-title">
                    <h6>Republic of the Philippines</h6>
                    <h6>Caraga Admininstrative Region</h6>
                    <h6>PROVINCE OF AGUSAN DEL NORTE</h6>
                    <h6>MINICIPALITY OF MAGALLANES</h6>
                </div>
                <img src="{{ asset('images/Municipal Logo of Magallanes.png') }}" alt="Municipal Logo of Magallanes.png"> <!-- Replace with actual logo path -->
            </div>

            <div class="letter-header">
                <p>Date: <span>${new Date().toLocaleDateString()}</span></p>
                <p>To: <strong>${document.getElementById("personnel_name").value}</strong></p>
                <p>Position: <span>${document.getElementById("personnel_position").value}</span></p>
                <p>Office: <span>${document.getElementById("personnel_office").value}</span></p>
            </div>

            <div class="letter-body">
                <p>Dear <strong>${document.getElementById("personnel_name").value}</strong>,</p>
                <p>
                    We are pleased to inform you that your salary has been adjusted as per the latest review.
                    Below are the details of your salary adjustment:
                </p>
                ${printContents} <!-- Display only the form contents as a table -->
            </div>

            <div class="signature">
                <p>Best regards,</p>
                <p><strong>[Authorized Signatory]</strong></p>
                <p>HR Department</p>
            </div>

            <div class="btn-container">
                <button class="print-btn btn btn-primary" onclick="window.print()">Print Letter</button>
                <button class="back-btn btn btn-secondary" onclick="restoreForm()">Go Back</button>
            </div>
        </div>
    `;
}

// Restore form when "Go Back" is clicked
function restoreForm() {
    location.reload(); // Reload page to restore the original form
}
