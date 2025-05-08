<?php

namespace App\Helpers;

class OfficeFormatter
{
    /**
     * Office name mapping - maps short codes to properly formatted full names
     */
    private static $officeNames = [
        'mayor' => 'OFFICE OF THE MAYOR',
        'mo' => 'OFFICE OF THE MAYOR',
        'sbo' => 'OFFICE OF THE SANGUNIANG BAYAN',
        'mpdo' => 'MUNICIPAL PLANNING & DEVELOPMENT COORDINATOR',
        'lcr' => 'OFFICE OF THE LOCAL REGISTRAR',
        'mbo' => 'OFFICE OF THE MUNICIPAL BUDGET OFFICER',
        'macco' => 'OFFICE OF THE MUNICIPAL ACCOUNTANT',
        'mto' => 'OFFICE OF THE MUNICIPAL TREASURER',
        'masso' => 'OFFICE OF THE MUNICIPAL ASSESSOR',
        'mho' => 'OFFICE OF THE MUNICIPAL HEALTH OFFICER',
        'mswdo' => 'SOCIAL WELFARE & DEVELOPMENT OFFICER',
        'mao' => 'OFFICE OF THE MUNICIPAL AGRICULTURIST',
        'meo' => 'OFFICE OF THE MUNICIPAL ENGINEER',
        'mee' => 'ERGONOMIC ENTERPRISE DEVELOPMENT MANAGEMENT',
        'mdrrmo' => 'LOCAL DISASTER RISK REDUCTION & MANAGEMENT',
        'hrmo' => 'HUMAN RESOURCE MANAGEMENT OFFICE',
        'gso' => 'GENERAL SERVICES OFFICE',
        'menro' => 'MUNICIPAL ENVIRONMENT AND NATURAL RESOURCES OFFICE'
    ];

    /**
     * Format office name to ensure it's properly capitalized and uses the full name
     * 
     * @param string $officeName The office name or code to format
     * @return string The properly formatted office name
     */
    public static function format($officeName)
    {
        if (empty($officeName)) {
            return '';
        }
        
        // Check if we have a direct mapping for this office code/name
        $lowerCaseOffice = strtolower(trim($officeName));
        
        // If we have a direct mapping, use it
        if (isset(self::$officeNames[$lowerCaseOffice])) {
            return self::$officeNames[$lowerCaseOffice];
        }
        
        // If no direct mapping, check if it's a partial match
        foreach (self::$officeNames as $code => $fullName) {
            if (strpos($lowerCaseOffice, $code) !== false || 
                strpos(strtolower($fullName), $lowerCaseOffice) !== false) {
                return $fullName;
            }
        }
        
        // If no mapping found, ensure it's properly capitalized
        return ucwords(strtolower($officeName));
    }
}