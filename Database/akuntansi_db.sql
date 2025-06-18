-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 03:54 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akuntansi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `account_type`) VALUES
(1, 'Kas', 'Asset'),
(2, 'Piutang Dagang', 'Asset'),
(3, 'Persediaan Barang Dagang', 'Asset'),
(4, 'Utang Dagang', 'Liability'),
(5, 'Penjualan', 'Revenue'),
(6, 'Pembelian', 'Expense'),
(7, 'Beban Angkut Pembelian', 'Expense'),
(8, 'Beban Angkut Penjualan', 'Expense'),
(9, 'Retur Penjualan', 'Revenue'),
(10, 'Retur Pembelian', 'Expense'),
(11, 'Potongan Penjualan', 'Revenue'),
(12, 'Potongan Pembelian', 'Expense'),
(13, 'Modal', 'Equity'),
(14, 'Prive', 'Equity'),
(15, 'Beban Gaji', 'Expense'),
(16, 'Beban Sewa', 'Expense');

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `id` int(11) NOT NULL,
  `report_type` enum('IncomeStatement','BalanceSheet','EquityChange') NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_reports`
--

INSERT INTO `financial_reports` (`id`, `report_type`, `period_start`, `period_end`, `data`, `created_at`) VALUES
(1, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":0,\"gross_profit\":200000,\"operating_expenses\":0,\"net_income\":200000}', '2025-06-15 07:04:03'),
(2, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":0,\"gross_profit\":200000,\"operating_expenses\":0,\"net_income\":200000}', '2025-06-15 07:09:41'),
(3, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":0,\"gross_profit\":200000,\"operating_expenses\":0,\"net_income\":200000}', '2025-06-15 07:10:20'),
(4, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":0,\"gross_profit\":200000,\"operating_expenses\":0,\"net_income\":200000}', '2025-06-15 07:10:24'),
(5, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":0,\"gross_profit\":200000,\"operating_expenses\":2000000,\"net_income\":-1800000}', '2025-06-15 07:12:40'),
(6, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":200000,\"cogs\":2000000,\"gross_profit\":-1800000,\"operating_expenses\":2000000,\"net_income\":-3800000}', '2025-06-15 07:18:26'),
(7, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":0,\"cogs\":0,\"gross_profit\":0,\"operating_expenses\":0,\"net_income\":0}', '2025-06-15 07:31:20'),
(8, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":0,\"cogs\":0,\"gross_profit\":0,\"operating_expenses\":0,\"net_income\":0}', '2025-06-15 07:31:55'),
(9, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":0,\"cogs\":0,\"gross_profit\":0,\"operating_expenses\":0,\"net_income\":0}', '2025-06-15 08:10:15'),
(10, 'IncomeStatement', '2025-06-01', '2025-06-30', '{\"net_sales\":0,\"cogs\":0,\"gross_profit\":0,\"operating_expenses\":0,\"net_income\":0}', '2025-06-15 08:11:49');

-- --------------------------------------------------------

--
-- Table structure for table `special_journals`
--

CREATE TABLE `special_journals` (
  `id` int(11) NOT NULL,
  `type` enum('CashReceipt','CashPayment','Purchase','Sale') NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `is_debit` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `date`, `description`, `debit_account_id`, `credit_account_id`, `amount`, `created_at`) VALUES
(7, '2025-06-28', 'beli makanan', 2, 1, 1000000.00, '2025-06-15 07:34:01'),
(8, '2025-06-10', 'gvgvg', 1, 1, 900000.00, '2025-06-15 08:09:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `special_journals`
--
ALTER TABLE `special_journals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `credit_account_id` (`credit_account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `special_journals`
--
ALTER TABLE `special_journals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `special_journals`
--
ALTER TABLE `special_journals`
  ADD CONSTRAINT `special_journals_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
