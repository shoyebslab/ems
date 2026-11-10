-- EMS database and sample employee data.

CREATE DATABASE IF NOT EXISTS `starline_exam` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `starline_exam`;

CREATE TABLE IF NOT EXISTS `employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_code` VARCHAR(50) NOT NULL UNIQUE,
    `employee_name` VARCHAR(100) NOT NULL,
    `department` VARCHAR(100) NOT NULL,
    `designation` VARCHAR(100) NOT NULL,
    `joining_date` DATE NOT NULL,
    `mobile_number` VARCHAR(20) NOT NULL,
    `salary` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert at least 5 sample employee records (as required)
INSERT IGNORE INTO `employees` (`employee_code`, `employee_name`, `department`, `designation`, `joining_date`, `mobile_number`, `salary`, `status`) VALUES
('SLG-1001', 'Tanvir Ahmed', 'IT & Software', 'Senior Software Programmer', '2022-01-15', '01711000111', 75000.00, 'Active'),
('SLG-1002', 'Farhana Yasmin', 'Human Resources', 'HR Manager', '2021-03-10', '01812000222', 65000.00, 'Active'),
('SLG-1003', 'Mahmudul Hasan', 'Accounts & Finance', 'Senior Accountant', '2020-07-01', '01913000333', 58000.00, 'Active'),
('SLG-1004', 'Nusrat Jahan', 'IT & Software', 'Database Administrator', '2023-02-20', '01614000444', 52000.00, 'Active'),
('SLG-1005', 'Kamrul Islam', 'Operations & Logistics', 'Operations Supervisor', '2019-11-05', '01515000555', 42000.00, 'Inactive'),
('SLG-1006', 'Sabrina Chowdhury', 'Marketing & Sales', 'Digital Marketing Executive', '2023-06-12', '01716000666', 38000.00, 'Active'),
('SLG-1007', 'Arifur Rahman', 'Accounts & Finance', 'Junior Accounts Officer', '2024-01-08', '01817000777', 32000.00, 'Active');
