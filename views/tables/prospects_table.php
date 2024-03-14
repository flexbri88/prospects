<?php

// defined('BASEPATH') or exit('No direct script access allowed');

// $CI = &get_instance();

// // Include the Composer autoload file
// require_once(__DIR__ . '../../../vendor/autoload.php');

// $keyFilePath = __DIR__ . '../../../config/linkedin-412422-7cbb846f8b97.json';
// $projectId = 'linkedin-412422';

// $bigQuery = new Google\Cloud\BigQuery\BigQueryClient([
//     'projectId' => $projectId,
//     'keyFilePath' => $keyFilePath
// ]);

// $query = 'SELECT * FROM `linkedin-412422.combined.final_normalized` LIMIT 10';
// $jobConfig = $bigQuery->query($query);
// $queryResults = $bigQuery->runQuery($jobConfig);

// $data = [];
// foreach ($queryResults as $row) {
//     $data[] = $row;
// }

// foreach ($data as $key=>$aRow) {

//     $row = [];

//     $row[] = $key+1;
//     $row[] = $aRow['name'];
//     $row[] = $aRow['email'];
//     $row[] = $aRow['education_level'];
//     $row[] = $aRow['religion'];
//     $row[] = $aRow['address'];

//     $row['DT_RowClass'] = 'has-row-options';

//     $output['aaData'][] = $row;

// }





