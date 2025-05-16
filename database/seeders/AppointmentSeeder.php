<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppointmentModel\Appointment;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        Appointment::create([
            'name' => 'Tonya Mark Nurse',
            'position' => 'Nurse',
            'rate_per_day' => 634.75,
            'employment_start' => '2023-03-26',
            'employment_end' => '2026-04-23',
            'source_of_fund' => 'General Fund',
            'location' => 'West Daniel',
            'office_assignment' => 'Social Welfare',
            'appointment_type' => 'job_order',
            'item_no' => null,
            'employee_id' => 'EMP001',
            'first_name' => 'Tonya',
            'middle_name' => 'Mark',
            'last_name' => 'Barry',
            'extension_name' => null,
            'gender' => 'male',
            'birthday' => '1967-08-11',
            'age' => 58,
        ]);

        $appointmentTypes = [
            'casual', 'contractual', 'coterminous', 'coterminousTemporary',
            'elected', 'permanent', 'provisional', 'regularPermanent', 'substitute', 'temporary', 'job_order'
        ];
        $officeAssignments = [
            'Office of the Mayor (MO)',
            'Office of the Sangguniang Bayan (SBO)',
            'Municipal Planning & Development Coordinator (MPDO)',
            'Office of the Local Civil Registrar (LCR)',
            'Office of the Municipal Budget Officer (MBO)',
            'Office of the Municipal Accountant (MACCO)',
            'Office of the Municipal Treasurer (MTO)',
            'Office of the Municipal Assessor (MASSO)',
            'Office of the Municipal Health Officer (MHO/RHU)',
            'Social Welfare & Development Officer (MSWDO)',
            'Office of the Municipal Agriculturist (MAO)',
            'Office of the Municipal Engineer (MEO)',
            'Ergonomic Enterprise Development Management (MEE)',
            'Local Disaster Risk Reduction & Management (MDRRMO)'
        ];
        $sourcesOfFund = ['General Fund', 'Special Fund', 'Trust Fund', 'Grant Fund'];
        $positions = ['Staff', 'Officer', 'Clerk', 'Engineer', 'Technician', 'Analyst', 'Assistant', 'Coordinator', 'Inspector'];

        for ($i = 2; $i <= 201; $i++) {
            $appointmentType = $appointmentTypes[array_rand($appointmentTypes)];
            $isJobOrder = $appointmentType === 'job_order';
            $firstName = $faker->firstName;
            $middleName = $faker->firstName;
            $lastName = $faker->lastName;
            $extensionName = $faker->boolean(10) ? $faker->suffix : null;
            $gender = $faker->randomElement(['male', 'female']);
            $birthday = $faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d');
            $age = date_diff(date_create($birthday), date_create('now'))->y;
            // Assign employment_start based on index for service year grouping
            if ($i <= 11) {
                // 10+ years
                $employmentStart = $faker->dateTimeBetween('-20 years', '-10 years')->format('Y-m-d');
            } elseif ($i <= 31) {
                // 5-9 years
                $employmentStart = $faker->dateTimeBetween('-9 years', '-5 years')->format('Y-m-d');
            } else {
                // Below 5 years
                $employmentStart = $faker->dateTimeBetween('-5 years', '-1 years')->format('Y-m-d');
            }
            $employmentEnd = $faker->dateTimeBetween('now', '+3 years')->format('Y-m-d');
            $ratePerDay = $faker->randomFloat(2, 400, 1500);
            $sourceOfFund = $sourcesOfFund[array_rand($sourcesOfFund)];
            $location = $faker->city;
            $officeAssignment = $officeAssignments[array_rand($officeAssignments)];
            $position = $positions[array_rand($positions)];
            
            // Only job_order gets employee_id, only non-job_order gets item_no
            $employeeId = $isJobOrder ? 'EMP' . str_pad($i, 3, '0', STR_PAD_LEFT) : null;
            $itemNo = $isJobOrder ? null : 'ITEM' . str_pad($i, 3, '0', STR_PAD_LEFT);

            Appointment::create([
                'name' => "$firstName $middleName $lastName",
                'position' => $position,
                'rate_per_day' => $ratePerDay,
                'employment_start' => $employmentStart,
                'employment_end' => $employmentEnd,
                'source_of_fund' => $sourceOfFund,
                'location' => $location,
                'office_assignment' => $officeAssignment,
                'appointment_type' => $appointmentType,
                'item_no' => $itemNo,
                'employee_id' => $employeeId,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'extension_name' => $extensionName,
                'gender' => $gender,
                'birthday' => $birthday,
                'age' => $age,
            ]);
        }
    }
}
