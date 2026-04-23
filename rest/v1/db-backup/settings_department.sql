-- ============================================================
-- 1. Create settings_department table
-- ============================================================
CREATE TABLE `settings_department` (
  `department_aid` int(11) NOT NULL AUTO_INCREMENT,
  `department_is_active` tinyint(4) NOT NULL DEFAULT 1,
  `department_name` varchar(200) NOT NULL,
  `department_created` datetime NOT NULL,
  `department_updated` datetime NOT NULL,
  PRIMARY KEY (`department_aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- 2. Add employee_department_id column to employees table
--    Run this AFTER creating settings_department above
-- ============================================================
ALTER TABLE `employees`
  ADD COLUMN `employee_department_id` int(11) NOT NULL DEFAULT 0
  AFTER `employee_email`;