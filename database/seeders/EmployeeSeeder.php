<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
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
        $appointmentTypes = [
            'casual', 'contractual', 'coterminous', 'coterminousTemporary',
            'elected', 'permanent', 'provisional', 'regularPermanent', 'substitute', 'temporary', 'job_order'
        ];
        $sourcesOfFund = ['General Fund', 'Special Fund', 'Grant', 'Donation'];
        $locations = ['West Daniel', 'South Park', 'East Bay', 'North Hill', 'Central City'];
        $genders = ['male', 'female'];
        $positions = ['Manager', 'Officer', 'Engineer', 'Technician', 'Analyst', 'Clerk', 'Coordinator', 'Inspector', 'Developer', 'Assistant'];
        $names = [
            ['John', 'A.', 'Smith', null],
            ['Maria', 'B.', 'Garcia', null],
            ['James', 'C.', 'Johnson', 'Jr.'],
            ['Patricia', 'D.', 'Williams', null],
            ['Robert', 'E.', 'Brown', null],
            ['Linda', 'F.', 'Jones', null],
            ['Michael', 'G.', 'Miller', null],
            ['Barbara', 'H.', 'Davis', null],
            ['William', 'I.', 'Wilson', null],
            ['Elizabeth', 'J.', 'Moore', null],
        ];

        foreach (range(1, 200) as $i) {
            $nameIdx = ($i - 1) % count($names);
            $positionIdx = ($i - 1) % count($positions);
            $day = str_pad((($i - 1) % 28) + 1, 2, '0', STR_PAD_LEFT); // Days 01-28
            $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT); // Months 01-12
            $birthYear = '198' . rand(0,9);
            DB::table('appointments')->insert([
                'name' => $names[$nameIdx][0] . ' ' . $names[$nameIdx][2],
                'position' => $positions[$positionIdx],
                'rate_per_day' => rand(500, 1500),
                'employment_start' => '2024-01-' . $day,
                'employment_end' => '2026-01-' . $day,
                'source_of_fund' => $sourcesOfFund[array_rand($sourcesOfFund)],
                'location' => $locations[array_rand($locations)],
                'office_assignment' => $officeAssignments[array_rand($officeAssignments)],
                'appointment_type' => $appointmentTypes[array_rand($appointmentTypes)],
                'item_no' => 'ITEM' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'employee_id' => 'EMP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'first_name' => $names[$nameIdx][0],
                'middle_name' => $names[$nameIdx][1],
                'last_name' => $names[$nameIdx][2],
                'extension_name' => $names[$nameIdx][3],
                'gender' => $genders[array_rand($genders)],
                'birthday' => $birthYear . '-' . $month . '-' . $day,
                'age' => rand(25, 60),
            ]);
        }
    }
}
