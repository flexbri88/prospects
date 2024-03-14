<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Prospects extends AdminController
{

    public $bigQuery;
    public function __construct()
    {
        parent::__construct();
        $this->bigQuery = $this->initializeBigQuery();
    }

    private function initializeBigQuery() {
        require_once(__DIR__ . '/../vendor/autoload.php');
        $keyFilePath = __DIR__ . '/../config/linkedin-412422-7cbb846f8b97.json';
        $projectId = 'linkedin-412422';

        $bigQuery = new Google\Cloud\BigQuery\BigQueryClient([
            'projectId' => $projectId,
            'keyFilePath' => $keyFilePath
        ]);
        return $bigQuery;
    }

    public function index()
    {
        $data['title']    = _l('prospects');
        $this->db->select('short_name');
        $countries = $this->db->get(db_prefix().'countries')->result_array();
        $data['countries']    = $countries;
        $this->db->where('staff_id', get_staff_user_id());
        $allFilters = $this->db->get(db_prefix().'prospect_filters')->result_array();
        $data['all_filters']    = $allFilters;
        $this->load->view('prospects', $data);
    }

    public function table()
    {
        // Pagination parameters sent by DataTables
        $start = isset($_GET['start']) ? $_GET['start'] : 0; // Start index of records to fetch
        $length = isset($_GET['length']) ? $_GET['length'] : 10; // Number of records per page
        $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : ''; // Search query
        $filterId = $_GET['filterId'];
        if ($filterId == 0 OR !empty($search)){
            // Query to get the total count of records
            $totalQuery = "SELECT COUNT(*) as total FROM `linkedin-412422.combined.final_normalized`";
            if (!empty($search)) {
                $search = strtolower($search); // Convert search phrase to lowercase
                $totalQuery .= " WHERE LOWER(name) LIKE '%$search%' OR LOWER(email) LIKE '%$search%' OR LOWER(education_level) LIKE '%$search%' OR LOWER(religion) LIKE '%$search%' OR LOWER(address) LIKE '%$search%' OR LOWER(political_party) LIKE '%$search%' OR LOWER(race) LIKE '%$search%' OR LOWER(zip) LIKE '%$search%' OR LOWER(seniority) LIKE '%$search%' OR LOWER(gender) LIKE '%$search%' OR LOWER(job_title) LIKE '%$search%' OR LOWER(city) LIKE '%$search%' OR LOWER(state) LIKE '%$search%' OR LOWER(country) LIKE '%$search%' OR LOWER(company_name) LIKE '%$search%' OR LOWER(salary) LIKE '%$search%' OR LOWER(job_summary) LIKE '%$search%' OR LOWER(industry) LIKE '%$search%' OR LOWER(company_website) LIKE '%$search%' OR LOWER(phones) LIKE '%$search%' OR LOWER(company_li) LIKE '%$search%'";
            }
            $totalJobConfig = $this->bigQuery->query($totalQuery);
            $totalQueryResults = $this->bigQuery->runQuery($totalJobConfig);
            $totalRow = $totalQueryResults->rows()->current();
            $totalRecords = $totalRow['total'];

            $query = "SELECT name, email, li_profile, education_level, religion, address FROM `linkedin-412422.combined.final_normalized`";

            if (!empty($search)) {
                $search = strtolower($search); // Convert search phrase to lowercase
                $query .= " WHERE LOWER(name) LIKE '%$search%' OR LOWER(email) LIKE '%$search%' OR LOWER(education_level) LIKE '%$search%' OR LOWER(religion) LIKE '%$search%' OR LOWER(address) LIKE '%$search%' OR LOWER(political_party) LIKE '%$search%' OR LOWER(race) LIKE '%$search%' OR LOWER(zip) LIKE '%$search%' OR LOWER(seniority) LIKE '%$search%' OR LOWER(gender) LIKE '%$search%' OR LOWER(job_title) LIKE '%$search%' OR LOWER(city) LIKE '%$search%' OR LOWER(state) LIKE '%$search%' OR LOWER(country) LIKE '%$search%' OR LOWER(company_name) LIKE '%$search%' OR LOWER(salary) LIKE '%$search%' OR LOWER(job_summary) LIKE '%$search%' OR LOWER(industry) LIKE '%$search%' OR LOWER(company_website) LIKE '%$search%' OR LOWER(phones) LIKE '%$search%' OR LOWER(company_li) LIKE '%$search%'";
            }

            $query .= " LIMIT $length OFFSET $start";
            $jobConfig = $this->bigQuery->query($query);
            $queryResults = $this->bigQuery->runQuery($jobConfig);
        }
        else{
            $this->db->where('id', $filterId);
            $filterData = $this->db->get(db_prefix().'prospect_filters')->row();
            // Query to get the total count of records
            $initialOperator = '';
            $operator = $filterData->operator == 'or' ? 'OR' : 'AND';
            $where = 'WHERE';
            $whereClause = '';

            $totalQuery = "SELECT COUNT(*) as total FROM `linkedin-412422.combined.final_normalized`";

            if ($filterData->has_email == 1){
                $whereClause .= " $initialOperator $where email IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_phone == 1){
                $whereClause .= " $initialOperator $where phones IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_address == 1){
                $whereClause .= " $initialOperator $where address IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_linkedin == 1){
                $whereClause .= " $initialOperator $where li_profile IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->part_of_company == 1){
                $whereClause .= " $initialOperator $where company_name IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }

            if ($filterData->gender != ''){
                $whereClause .= " $initialOperator $where gender = '$filterData->gender'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->political_affiliation != ''){
                $whereClause .= " $initialOperator $where political_party = '$filterData->political_affiliation'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->country != ''){
                $whereClause .= " $initialOperator $where country = '$filterData->country'";
                $where = '';
                $initialOperator = $operator;
            }

            if ($filterData->state != ''){
                $whereClause .= " $initialOperator $where state = '$filterData->state'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->zip_code != ''){
                $whereClause .= " $initialOperator $where zip = '$filterData->zip_code'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->job_position != ''){
                $whereClause .= " $initialOperator $where job_title = '$filterData->job_position'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->industry != ''){
                $whereClause .= " $initialOperator $where industry = '$filterData->industry'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->salary != ''){
                $whereClause .= " $initialOperator $where salary = '$filterData->salary'";
                $where = '';
                $initialOperator = $operator;
            }
            $totalQuery .= $whereClause;
            $totalJobConfig = $this->bigQuery->query($totalQuery);
            $totalQueryResults = $this->bigQuery->runQuery($totalJobConfig);
            $totalRow = $totalQueryResults->rows()->current();
            $totalRecords = $totalRow['total'];

            $totalData = array(
                'total_results' => $totalRecords,
            );

            $this->db->where('id', $filterId);
            $this->db->update('prospect_filters', $totalData);

            $query = "SELECT name, email, li_profile, education_level, religion, address FROM `linkedin-412422.combined.final_normalized`";

            $query .= $whereClause;

            $query .= " LIMIT $length OFFSET $start";
            $jobConfig = $this->bigQuery->query($query);
            $queryResults = $this->bigQuery->runQuery($jobConfig);
        }

        $filteredRecords = $totalRecords;

        $output['draw'] = $_GET['draw'] ?? 1; // DataTables draw counter
        $output['recordsTotal'] = $totalRecords; // Total records in the dataset
        $output['recordsFiltered'] = $filteredRecords; // Total records after filtering

        foreach ($queryResults as $key => $aRow) {
            $row = [];
            // $row['DTRowIndex'] = $key + 1;
            $row['name'] = $aRow['name'];
            $row['email'] = $aRow['email'];
            $row['li_profile'] = $aRow['li_profile'] ? '<a href="https://'.$aRow['li_profile'].'" target="_blank"> View Profile</a>' : '';
            $row['education_level'] = $aRow['education_level'];
            $row['religion'] = $aRow['religion'];
            $row['address'] = $aRow['address'];
            // $row[] = 'has-row-options';

            $output['data'][] = $row;
        }

        echo json_encode($output);
        // return $this->app->get_table_data(module_views_path('prospects', 'tables/prospects_table'));
    }

    public function saveFilters()
    {
        if ($this->input->is_ajax_request()) {
            // Get data from AJAX request
            $data = $this->input->post();

            $data['staff_id'] = get_staff_user_id();

            // Save data to database
            $result = $this->db->insert(db_prefix()."prospect_filters", $data);
            $inserted_id = $this->db->insert_id();

            // Check if data was saved successfully
            if ($inserted_id) {
                $response['status'] = 'success';
                $response['message'] = 'Data saved successfully.';
                $response['inserted_id'] = $inserted_id;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to save data.';
            }
            // Return JSON response
            echo json_encode($response);
        } else {
            show_404(); // or handle unauthorized access
        }
    }

    public function getFilterDetails()
    {
        if ($this->input->is_ajax_request()) {
            // Get data from AJAX request
            $filter_id = $this->input->post('filter_id');

            $this->db->where('id', $filter_id);
            $data = $this->db->get(db_prefix()."prospect_filters")->row();
            // Check if data was saved successfully
            if ($data) {
                $response['status'] = 'success';
                $response['message'] = 'Data fetched successfully.';
                $response['data'] = $data;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to get data.';
                $response['data'] = 'Failed to get data.';
            }
            // Return JSON response
            echo json_encode($response);
        } else {
            show_404(); // or handle unauthorized access
        }
    }

    public function deleteFilter()
    {
        if ($this->input->is_ajax_request()) {
            // Get data from AJAX request
            $filter_id = $this->input->post('filter_id');

            $this->db->where('id', $filter_id);
            $this->db->delete(db_prefix()."prospect_filters");

            if ($this->db->affected_rows() > 0) {
                // Deletion successful
                $response['status'] = 'success';
                $response['message'] = 'Data deleted successfully.';
            } else {
                // No rows deleted or an error occurred
                $response['status'] = 'error';
                $response['message'] = 'Failed to delete data.';
            }
            // Return JSON response
            echo json_encode($response);
        } else {
            show_404(); // or handle unauthorized access
        }
    }

    public function downloadCsv($filter_id = 0){
        // Set memory limit
        ini_set('memory_limit', '1024M');

// Define header row
        $header = array("name", "gender", "email", "education_level", "li_profile", "religion", "race", "political_party", "address", "country", "state", "city", "zip", "company_name", "seniority", "job_title", "salary", "years_experience", "job_summary", "industry", "company_website", "company_li", "phones");

        $this->db->where('id', $filter_id);
        $filterData = $this->db->get(db_prefix().'prospect_filters')->row();

// Initialize variables
        $interval = 50000;
        $offset = $filterData->download_start_limit;
        $batchSize = $interval;
        $totalRecords = 300000;
        $recordCount = 0;
        $worksheetCount = 1;
        $innerLimitReach = false;
        $nextOffset = $offset + $totalRecords;

// Open file handler for the first worksheet
        $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');

// Write header row
        fputcsv($file[$worksheetCount], $header);

// Fetch data from function in batches
        while ($recordCount <= $totalRecords) {
//            set the remaining records from 300k
            $remainingRecords = $totalRecords - $recordCount;
            if ($remainingRecords < $interval){
                $batchSize = $offset + $remainingRecords;
            }

            $batch = $this->get_data_batch($offset, $batchSize, $filterData);

            if (!$batch){
                break;
            }
            // Write data rows
            foreach ($batch as $aRow) {
                $row = [];
                $row['name'] = $aRow['name'];
                $row['gender'] = $aRow['gender'];
                $row['email'] = $aRow['email'];
                $row['education_level'] = $aRow['education_level'];
                $row['li_profile'] = $aRow['li_profile'];
                $row['religion'] = $aRow['religion'];
                $row['race'] = $aRow['race'];
                $row['political_party'] = $aRow['political_party'];
                $row['address'] = $aRow['address'];
                $row['country'] = $aRow['country'];
                $row['state'] = $aRow['state'];
                $row['city'] = $aRow['city'];
                $row['zip'] = $aRow['zip'];
                $row['company_name'] = $aRow['company_name'];
                $row['seniority'] = $aRow['seniority'];
                $row['job_title'] = $aRow['job_title'];
                $row['salary'] = $aRow['salary'];
                $row['years_experience'] = $aRow['years_experience'];
                $row['job_summary'] = $aRow['job_summary'];
                $row['industry'] = $aRow['industry'];
                $row['company_website'] = $aRow['company_website'];
                $row['company_li'] = $aRow['company_li'];
                $row['phones'] = $aRow['phones'];

                fputcsv($file[$worksheetCount], $row);
                $recordCount++;

                if ($recordCount > $totalRecords){
                    $updatedOffset = array(
                        'download_start_limit' => $nextOffset,
                    );
                    $this->db->where('id', $filter_id);
                    $this->db->update('prospect_filters', $updatedOffset);
                    $innerLimitReach = true;
                    break;
                }

//            Check if the results exceeds the total records found in filter
                $totalExecutedRecords = $offset + $recordCount;
                if($totalExecutedRecords > $filterData->total_results){
                    $updatedOffset = array(
                        'download_start_limit' => 0,
                    );
                    $this->db->where('id', $filter_id);
                    $this->db->update('prospect_filters', $updatedOffset);
                    $innerLimitReach = true;
                    break;
                }

            }
            // Flush output buffer to prevent memory exhaustion
            ob_flush();
            if ($innerLimitReach){
                break;
            }
            $offset = $offset + $interval;
            $batchSize = $batchSize + $interval;
        }

// Close the last file handler
        fclose($file[$worksheetCount]);

        //
// Compress all CSV files into a ZIP archive (optional)
        $zip = new ZipArchive();
        $zipFileName = 'worksheets.zip';
        $zip->open($zipFileName, ZipArchive::CREATE);
        for ($i = 1; $i <= $worksheetCount; $i++) {
            $zip->addFile("worksheet_$i.csv", "worksheet_$i.csv");
        }
        $zip->close();

        // Clean up: Delete individual CSV files (optional)
        for ($i = 1; $i <= $worksheetCount; $i++) {
            unlink("worksheet_$i.csv");
        }
// Clear output buffer and end buffering
        ob_end_clean();

// Set headers to force download the ZIP file
        header("Content-type: application/zip");
        header("Content-disposition: attachment; filename=\"$zipFileName\"");
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);
        unlink($zipFileName);




//Script to download multiple files in zip file
        /*// Set memory limit
                ini_set('memory_limit', '512M');

        // Define header row
                $header = array("name", "gender", "email", "education_level", "li_profile", "religion", "race", "political_party", "address", "country", "state", "city", "zip", "company_name", "seniority", "job_title", "salary", "years_experience", "job_summary", "industry", "company_website", "company_li", "phones");

        // Initialize variables
                $offset = 0;
                $batchSize = 500;
                $totalRecords = 10000;
                $worksheetThreshold = 5000;
                $recordCount = 0;
                $worksheetCount = 1;

        // Open file handler for the first worksheet
                $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');

        // Write header row
                fputcsv($file[$worksheetCount], $header);

        // Fetch data from function in batches
                while ($batch = $this->get_data_batch($offset, $batchSize, $filter_id)) {
                    if ($recordCount >= $totalRecords) {
                        break;
                    }
                    // Write data rows
                    foreach ($batch as $row) {
                        fputcsv($file[$worksheetCount], $row);
                        $recordCount++;

                        // Check if it's time to start a new worksheet
                        if ($recordCount % $worksheetThreshold === 0) {
                            // Close the current file handler
                            fclose($file[$worksheetCount]);

                            // Increment worksheet count and open a new file handler for the next worksheet
                            $worksheetCount++;
                            $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');

                            // Write header row for the new worksheet
                            fputcsv($file[$worksheetCount], $header);
                        }
                    }
                    // Flush output buffer to prevent memory exhaustion
        //            ob_flush();
        //            flush();
                    $offset += $batchSize;
                    $batchSize += $recordCount;
                }

        // Close the last file handler
                fclose($file[$worksheetCount]);

                //
        // Compress all CSV files into a ZIP archive (optional)
                $zip = new ZipArchive();
                $zipFileName = 'worksheets.zip';
                $zip->open($zipFileName, ZipArchive::CREATE);
                for ($i = 1; $i <= $worksheetCount; $i++) {
                    $zip->addFile("worksheet_$i.csv", "worksheet_$i.csv");
                }
                $zip->close();

                // Clean up: Delete individual CSV files (optional)
                for ($i = 1; $i <= $worksheetCount; $i++) {
                    unlink("worksheet_$i.csv");
                }
        // Clear output buffer and end buffering
                ob_end_clean();

        // Set headers to force download the ZIP file
                header("Content-type: application/zip");
                header("Content-disposition: attachment; filename=\"$zipFileName\"");
                header('Content-Length: ' . filesize($zipFileName));
                readfile($zipFileName);
                unlink($zipFileName);*/
    }

    public function get_data_batch($offset, $batchSize, $filterData){
        $start = $offset; // Start index of records to fetch
        $length = $batchSize; // Number of records per page
//        $filterId = $filter_id;

//        $this->db->where('id', $filterId);
//        $filterData = $this->db->get(db_prefix().'prospect_filters')->row();

        $whereClause = '';

        if ($filterData){
            $initialOperator = '';
            $operator = $filterData->operator == 'or' ? 'OR' : 'AND';
            $where = 'WHERE';


            if ($filterData->has_email == 1){
                $whereClause .= " $initialOperator $where email IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_phone == 1){
                $whereClause .= " $initialOperator $where phones IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_address == 1){
                $whereClause .= " $initialOperator $where address IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->has_linkedin == 1){
                $whereClause .= " $initialOperator $where li_profile IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->part_of_company == 1){
                $whereClause .= " $initialOperator $where company_name IS NOT NULL";
                $where = '';
                $initialOperator = $operator;
            }

            if ($filterData->gender != ''){
                $whereClause .= " $initialOperator $where gender = '$filterData->gender'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->political_affiliation != ''){
                $whereClause .= " $initialOperator $where political_party = '$filterData->political_affiliation'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->country != ''){
                $whereClause .= " $initialOperator $where country = '$filterData->country'";
                $where = '';
                $initialOperator = $operator;
            }

            if ($filterData->state != ''){
                $whereClause .= " $initialOperator $where state = '$filterData->state'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->zip_code != ''){
                $whereClause .= " $initialOperator $where zip = '$filterData->zip_code'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->job_position != ''){
                $whereClause .= " $initialOperator $where job_title = '$filterData->job_position'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->industry != ''){
                $whereClause .= " $initialOperator $where industry = '$filterData->industry'";
                $where = '';
                $initialOperator = $operator;
            }
            if ($filterData->salary != ''){
                $whereClause .= " $initialOperator $where salary = '$filterData->salary'";
                $where = '';
                $initialOperator = $operator;
            }
        }

//        $query = "SELECT name, email, li_profile, education_level, religion, address FROM `linkedin-412422.combined.final_normalized`";
        $query = "SELECT * FROM `linkedin-412422.combined.final_normalized`";

        $query .= $whereClause;

        $query .= " LIMIT $length OFFSET $start";
        $jobConfig = $this->bigQuery->query($query);
        $queryResults = $this->bigQuery->runQuery($jobConfig);
        return $queryResults;

        /*$output['draw'] = $_GET['draw'] ?? 1; // DataTables draw counter

        foreach ($queryResults as $key => $aRow) {
            $row = [];
            $row['name'] = $aRow['name'];
            $row['gender'] = $aRow['gender'];
            $row['email'] = $aRow['email'];
            $row['education_level'] = $aRow['education_level'];
            $row['li_profile'] = $aRow['li_profile'];
            $row['religion'] = $aRow['religion'];
            $row['race'] = $aRow['race'];
            $row['political_party'] = $aRow['political_party'];
            $row['address'] = $aRow['address'];
            $row['country'] = $aRow['country'];
            $row['state'] = $aRow['state'];
            $row['city'] = $aRow['city'];
            $row['zip'] = $aRow['zip'];
            $row['company_name'] = $aRow['company_name'];
            $row['seniority'] = $aRow['seniority'];
            $row['job_title'] = $aRow['job_title'];
            $row['salary'] = $aRow['salary'];
            $row['years_experience'] = $aRow['years_experience'];
            $row['job_summary'] = $aRow['job_summary'];
            $row['industry'] = $aRow['industry'];
            $row['company_website'] = $aRow['company_website'];
            $row['company_li'] = $aRow['company_li'];
            $row['phones'] = $aRow['phones'];



            $output['data'][] = $row;
        }
        return $output['data'];*/
    }

//    public function downloadCsv($filter_id = 0){
//        // Set memory limit
//        ini_set('memory_limit', '1024M');
//
//// Define header row
//        $header = array("name", "gender", "email", "education_level", "li_profile", "religion", "race", "political_party", "address", "country", "state", "city", "zip", "company_name", "seniority", "job_title", "salary", "years_experience", "job_summary", "industry", "company_website", "company_li", "phones");
//
//// Initialize variables
//        $interval = 50000;
//
//        $offset = 0;
//        $batchSize = $interval;
//        $totalRecords = 300000;
//        $worksheetThreshold = 50000;
//        $recordCount = 0;
//        $worksheetCount = 1;
//        $innerLimitReach = false;
//
//// Open file handler for the first worksheet
//        $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');
//
//// Write header row
//        fputcsv($file[$worksheetCount], $header);
//
//// Fetch data from function in batches
//        while ($recordCount <= $totalRecords) {
//            $remainingRecords = $totalRecords - $recordCount;
//            if ($remainingRecords < $interval){
//                $batchSize = $offset + $remainingRecords;
//            }
//
//            $batch = $this->get_data_batch($offset, $batchSize, $filter_id);
//            if (!$batch){
//                break;
//            }
//            // Write data rows
//            foreach ($batch as $aRow) {
//                $row = [];
//                $row['name'] = $aRow['name'];
//                $row['gender'] = $aRow['gender'];
//                $row['email'] = $aRow['email'];
//                $row['education_level'] = $aRow['education_level'];
//                $row['li_profile'] = $aRow['li_profile'];
//                $row['religion'] = $aRow['religion'];
//                $row['race'] = $aRow['race'];
//                $row['political_party'] = $aRow['political_party'];
//                $row['address'] = $aRow['address'];
//                $row['country'] = $aRow['country'];
//                $row['state'] = $aRow['state'];
//                $row['city'] = $aRow['city'];
//                $row['zip'] = $aRow['zip'];
//                $row['company_name'] = $aRow['company_name'];
//                $row['seniority'] = $aRow['seniority'];
//                $row['job_title'] = $aRow['job_title'];
//                $row['salary'] = $aRow['salary'];
//                $row['years_experience'] = $aRow['years_experience'];
//                $row['job_summary'] = $aRow['job_summary'];
//                $row['industry'] = $aRow['industry'];
//                $row['company_website'] = $aRow['company_website'];
//                $row['company_li'] = $aRow['company_li'];
//                $row['phones'] = $aRow['phones'];
//
//                fputcsv($file[$worksheetCount], $row);
//                $recordCount++;
//                if ($recordCount > $totalRecords){
//                    $innerLimitReach = true;
//                    break;
//                }
//            }
//            // Flush output buffer to prevent memory exhaustion
//            ob_flush();
//            if ($innerLimitReach){
//                break;
//            }
//            $offset = $offset + $interval;
//            $batchSize = $batchSize + $interval;
//        }
//
//// Close the last file handler
//        fclose($file[$worksheetCount]);
//
//        //
//// Compress all CSV files into a ZIP archive (optional)
//        $zip = new ZipArchive();
//        $zipFileName = 'worksheets.zip';
//        $zip->open($zipFileName, ZipArchive::CREATE);
//        for ($i = 1; $i <= $worksheetCount; $i++) {
//            $zip->addFile("worksheet_$i.csv", "worksheet_$i.csv");
//        }
//        $zip->close();
//
//        // Clean up: Delete individual CSV files (optional)
//        for ($i = 1; $i <= $worksheetCount; $i++) {
//            unlink("worksheet_$i.csv");
//        }
//// Clear output buffer and end buffering
//        ob_end_clean();
//
//// Set headers to force download the ZIP file
//        header("Content-type: application/zip");
//        header("Content-disposition: attachment; filename=\"$zipFileName\"");
//        header('Content-Length: ' . filesize($zipFileName));
//        readfile($zipFileName);
//        unlink($zipFileName);
//
//
//
//
////Script to download multiple files in zip file
///*// Set memory limit
//        ini_set('memory_limit', '512M');
//
//// Define header row
//        $header = array("name", "gender", "email", "education_level", "li_profile", "religion", "race", "political_party", "address", "country", "state", "city", "zip", "company_name", "seniority", "job_title", "salary", "years_experience", "job_summary", "industry", "company_website", "company_li", "phones");
//
//// Initialize variables
//        $offset = 0;
//        $batchSize = 500;
//        $totalRecords = 10000;
//        $worksheetThreshold = 5000;
//        $recordCount = 0;
//        $worksheetCount = 1;
//
//// Open file handler for the first worksheet
//        $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');
//
//// Write header row
//        fputcsv($file[$worksheetCount], $header);
//
//// Fetch data from function in batches
//        while ($batch = $this->get_data_batch($offset, $batchSize, $filter_id)) {
//            if ($recordCount >= $totalRecords) {
//                break;
//            }
//            // Write data rows
//            foreach ($batch as $row) {
//                fputcsv($file[$worksheetCount], $row);
//                $recordCount++;
//
//                // Check if it's time to start a new worksheet
//                if ($recordCount % $worksheetThreshold === 0) {
//                    // Close the current file handler
//                    fclose($file[$worksheetCount]);
//
//                    // Increment worksheet count and open a new file handler for the next worksheet
//                    $worksheetCount++;
//                    $file[$worksheetCount] = fopen("worksheet_$worksheetCount.csv", 'w');
//
//                    // Write header row for the new worksheet
//                    fputcsv($file[$worksheetCount], $header);
//                }
//            }
//            // Flush output buffer to prevent memory exhaustion
////            ob_flush();
////            flush();
//            $offset += $batchSize;
//            $batchSize += $recordCount;
//        }
//
//// Close the last file handler
//        fclose($file[$worksheetCount]);
//
//        //
//// Compress all CSV files into a ZIP archive (optional)
//        $zip = new ZipArchive();
//        $zipFileName = 'worksheets.zip';
//        $zip->open($zipFileName, ZipArchive::CREATE);
//        for ($i = 1; $i <= $worksheetCount; $i++) {
//            $zip->addFile("worksheet_$i.csv", "worksheet_$i.csv");
//        }
//        $zip->close();
//
//        // Clean up: Delete individual CSV files (optional)
//        for ($i = 1; $i <= $worksheetCount; $i++) {
//            unlink("worksheet_$i.csv");
//        }
//// Clear output buffer and end buffering
//        ob_end_clean();
//
//// Set headers to force download the ZIP file
//        header("Content-type: application/zip");
//        header("Content-disposition: attachment; filename=\"$zipFileName\"");
//        header('Content-Length: ' . filesize($zipFileName));
//        readfile($zipFileName);
//        unlink($zipFileName);*/
//    }
//
//    public function get_data_batch($offset, $batchSize, $filter_id = 0){
//        $start = $offset; // Start index of records to fetch
//        $length = $batchSize; // Number of records per page
//        $filterId = $filter_id;
//
//        $this->db->where('id', $filterId);
//        $filterData = $this->db->get(db_prefix().'prospect_filters')->row();
//
//        $whereClause = '';
//
//        if ($filterData){
//            $initialOperator = '';
//            $operator = $filterData->operator == 'or' ? 'OR' : 'AND';
//            $where = 'WHERE';
//
//
//            if ($filterData->has_email == 1){
//                $whereClause .= " $initialOperator $where email IS NOT NULL";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->has_phone == 1){
//                $whereClause .= " $initialOperator $where phones IS NOT NULL";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->has_address == 1){
//                $whereClause .= " $initialOperator $where address IS NOT NULL";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->has_linkedin == 1){
//                $whereClause .= " $initialOperator $where li_profile IS NOT NULL";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->part_of_company == 1){
//                $whereClause .= " $initialOperator $where company_name IS NOT NULL";
//                $where = '';
//                $initialOperator = $operator;
//            }
//
//            if ($filterData->gender != ''){
//                $whereClause .= " $initialOperator $where gender = '$filterData->gender'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->political_affiliation != ''){
//                $whereClause .= " $initialOperator $where political_party = '$filterData->political_affiliation'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->country != ''){
//                $whereClause .= " $initialOperator $where country = '$filterData->country'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//
//            if ($filterData->state != ''){
//                $whereClause .= " $initialOperator $where state = '$filterData->state'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->zip_code != ''){
//                $whereClause .= " $initialOperator $where zip = '$filterData->zip_code'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->job_position != ''){
//                $whereClause .= " $initialOperator $where job_title = '$filterData->job_position'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->industry != ''){
//                $whereClause .= " $initialOperator $where industry = '$filterData->industry'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//            if ($filterData->salary != ''){
//                $whereClause .= " $initialOperator $where salary = '$filterData->salary'";
//                $where = '';
//                $initialOperator = $operator;
//            }
//        }
//
////        $query = "SELECT name, email, li_profile, education_level, religion, address FROM `linkedin-412422.combined.final_normalized`";
//        $query = "SELECT * FROM `linkedin-412422.combined.final_normalized`";
//
//        $query .= $whereClause;
//
//        $query .= " LIMIT $length OFFSET $start";
//        $jobConfig = $this->bigQuery->query($query);
//        $queryResults = $this->bigQuery->runQuery($jobConfig);
//        return $queryResults;
//
//        /*$output['draw'] = $_GET['draw'] ?? 1; // DataTables draw counter
//
//        foreach ($queryResults as $key => $aRow) {
//            $row = [];
//            $row['name'] = $aRow['name'];
//            $row['gender'] = $aRow['gender'];
//            $row['email'] = $aRow['email'];
//            $row['education_level'] = $aRow['education_level'];
//            $row['li_profile'] = $aRow['li_profile'];
//            $row['religion'] = $aRow['religion'];
//            $row['race'] = $aRow['race'];
//            $row['political_party'] = $aRow['political_party'];
//            $row['address'] = $aRow['address'];
//            $row['country'] = $aRow['country'];
//            $row['state'] = $aRow['state'];
//            $row['city'] = $aRow['city'];
//            $row['zip'] = $aRow['zip'];
//            $row['company_name'] = $aRow['company_name'];
//            $row['seniority'] = $aRow['seniority'];
//            $row['job_title'] = $aRow['job_title'];
//            $row['salary'] = $aRow['salary'];
//            $row['years_experience'] = $aRow['years_experience'];
//            $row['job_summary'] = $aRow['job_summary'];
//            $row['industry'] = $aRow['industry'];
//            $row['company_website'] = $aRow['company_website'];
//            $row['company_li'] = $aRow['company_li'];
//            $row['phones'] = $aRow['phones'];
//
//
//
//            $output['data'][] = $row;
//        }
//        return $output['data'];*/
//    }

}
