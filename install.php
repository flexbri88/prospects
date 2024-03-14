<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'prospect_filters')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "prospect_filters` (
  `id` int(11) NOT NULL,
  `filter_name` mediumtext NOT NULL,
  `default_filter` int(11) DEFAULT '0',
  `operator` mediumtext NOT NULL,
  `staff_id` int(11) NOT NULL,
  `has_email` int(11) DEFAULT '0',
  `has_phone` int(11) DEFAULT '0',
  `has_address` int(11) DEFAULT '0',
  `has_linkedin` int(11) DEFAULT '0',
  `part_of_company` int(11) DEFAULT '0',
  `gender` mediumtext DEFAULT NULL,
  `country` mediumtext DEFAULT NULL,
  `state` text DEFAULT NULL,
  `political_affiliation` text DEFAULT NULL,
  `zip_code` text DEFAULT NULL,
  `job_position` text DEFAULT NULL,
  `industry` text DEFAULT NULL,
  `salary` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'prospect_filters`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'prospect_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->field_exists('download_start_limit', db_prefix() .'prospect_filters')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'prospect_filters` 
      ADD COLUMN `downlaod_start_limit` int(11) NULL DEFAULT 0;');
}

if (!$CI->db->field_exists('download_end_limit', db_prefix() .'prospect_filters')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'prospect_filters` 
      ADD COLUMN `downlaod_end_limit` int(11) NOT NULL DEFAULT 300000;');
}

if (!$CI->db->field_exists('total_results', db_prefix() .'prospect_filters')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'prospect_filters` 
      ADD COLUMN `total_results` int(11) NOT NULL DEFAULT 0;');
}