<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DCT_Ajax {

    public function init() {
        $actions = array(
            'dct_get_projects', 'dct_save_project', 'dct_delete_project',
            'dct_get_stakeholders', 'dct_save_stakeholder', 'dct_delete_stakeholder',
            'dct_get_project_stakeholders', 'dct_assign_stakeholder', 'dct_unassign_stakeholder',
            'dct_get_transactions', 'dct_save_transaction', 'dct_delete_transaction',
            'dct_get_summary', 'dct_get_all_summary', 'dct_get_category_costs',
            'dct_export_data',
            'dct_import_prepare', 'dct_import_process', 'dct_import_data',
        );
        foreach ( $actions as $action ) {
            add_action( "wp_ajax_{$action}", array( $this, str_replace( 'dct_', '', $action ) ) );
        }

        $public_actions = array( 'dct_get_summary', 'dct_get_all_summary', 'dct_get_stakeholders', 'dct_get_projects' );
        foreach ( $public_actions as $action ) {
            add_action( "wp_ajax_nopriv_{$action}", array( $this, str_replace( 'dct_', '', $action ) ) );
        }
    }

    private function verify() {
        if ( ! check_ajax_referer( 'dct_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized', 403 );
        }
    }

    private function verify_public() {
        if ( ! check_ajax_referer( 'dct_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Unauthorized', 403 );
        }
    }

    private function ok( $data = array() )  { wp_send_json_success( $data ); }
    private function err( $msg )            { wp_send_json_error( $msg ); }

    /* ── Projects ── */

    public function get_projects() {
        if ( current_user_can( 'manage_options' ) ) {
            $this->verify();
        } else {
            $this->verify_public();
        }
        $this->ok( DCT_DB::get_projects() );
    }

    public function save_project() {
        $this->verify();
        $data = $_POST;
        if ( empty( $data['name'] ) ) { $this->err( 'Project name is required.' ); return; }

        if ( ! empty( $data['id'] ) ) {
            DCT_DB::update_project( intval( $data['id'] ), $data );
            $this->ok( array( 'id' => intval( $data['id'] ), 'message' => 'Project updated.' ) );
        } else {
            $id = DCT_DB::insert_project( $data );
            $this->ok( array( 'id' => $id, 'message' => 'Project added.' ) );
        }
    }

    public function delete_project() {
        $this->verify();
        $id = intval( $_POST['id'] );
        if ( ! $id ) { $this->err( 'Invalid ID.' ); return; }
        DCT_DB::delete_project( $id );
        $this->ok( array( 'message' => 'Project deleted.' ) );
    }

    /* ── Stakeholders ── */

    public function get_stakeholders() {
        if ( current_user_can( 'manage_options' ) ) {
            $this->verify();
        } else {
            $this->verify_public();
        }
        $this->ok( DCT_DB::get_stakeholders() );
    }

    public function save_stakeholder() {
        $this->verify();
        $data = $_POST;
        if ( empty( $data['name'] ) ) { $this->err( 'Name is required.' ); return; }

        if ( ! empty( $data['id'] ) ) {
            DCT_DB::update_stakeholder( intval( $data['id'] ), $data );
            $this->ok( array( 'id' => intval( $data['id'] ), 'message' => 'Stakeholder updated.' ) );
        } else {
            $id = DCT_DB::insert_stakeholder( $data );
            $this->ok( array( 'id' => $id, 'message' => 'Stakeholder added.' ) );
        }
    }

    public function delete_stakeholder() {
        $this->verify();
        $id = intval( $_POST['id'] );
        if ( ! $id ) { $this->err( 'Invalid ID.' ); return; }
        DCT_DB::delete_stakeholder( $id );
        $this->ok( array( 'message' => 'Stakeholder deleted.' ) );
    }

    /* ── Assignments ── */

    public function get_project_stakeholders() {
        $this->verify();
        $pid = intval( $_POST['project_id'] );
        $this->ok( DCT_DB::get_project_stakeholders( $pid ) );
    }

    public function assign_stakeholder() {
        $this->verify();
        $pid = intval( $_POST['project_id'] );
        $sid = intval( $_POST['stakeholder_id'] );
        if ( ! $pid || ! $sid ) { $this->err( 'Invalid data.' ); return; }
        DCT_DB::assign_stakeholder( $pid, $sid );
        $this->ok( array( 'message' => 'Assigned.' ) );
    }

    public function unassign_stakeholder() {
        $this->verify();
        $pid = intval( $_POST['project_id'] );
        $sid = intval( $_POST['stakeholder_id'] );
        DCT_DB::unassign_stakeholder( $pid, $sid );
        $this->ok( array( 'message' => 'Unassigned.' ) );
    }

    /* ── Transactions ── */

    public function get_transactions() {
        $this->verify();
        $filters = array(
            'project_id'           => intval( $_POST['project_id'] ?? 0 ),
            'stakeholder_id'       => intval( $_POST['stakeholder_id'] ?? 0 ),
            'transaction_type'     => sanitize_text_field( $_POST['transaction_type'] ?? '' ),
            'from_stakeholder_id'  => intval( $_POST['from_stakeholder_id'] ?? 0 ),
            'to_stakeholder_id'    => intval( $_POST['to_stakeholder_id'] ?? 0 ),
            'category'             => sanitize_text_field( $_POST['category'] ?? '' ),
            'phase'                => sanitize_text_field( $_POST['phase'] ?? '' ),
            'date_from'            => sanitize_text_field( $_POST['date_from'] ?? '' ),
            'date_to'              => sanitize_text_field( $_POST['date_to'] ?? '' ),
        );
        $this->ok( DCT_DB::get_transactions( $filters ) );
    }

    public function save_transaction() {
        $this->verify();
        $data = $_POST;
        if ( empty( $data['project_id'] ) )       { $this->err( 'Select a project.' ); return; }
        if ( empty( $data['amount'] ) || floatval($data['amount']) <= 0 ) { $this->err( 'Enter a valid amount.' ); return; }
        if ( empty( $data['transaction_date'] ) )  { $this->err( 'Select a date.' ); return; }
        if ( $data['transaction_type'] === 'transfer' && empty( $data['from_stakeholder_id'] ) && empty( $data['to_stakeholder_id'] ) ) {
            $this->err( 'At least one party required for a transfer.' ); return;
        }
        if ( $data['transaction_type'] === 'expense' && empty( $data['from_stakeholder_id'] ) ) {
            $this->err( 'Who paid this expense?' ); return;
        }

        if ( ! empty( $data['id'] ) ) {
            DCT_DB::update_transaction( intval( $data['id'] ), $data );
            $this->ok( array( 'id' => intval( $data['id'] ), 'message' => 'Transaction updated.' ) );
        } else {
            $id = DCT_DB::insert_transaction( $data );
            $this->ok( array( 'id' => $id, 'message' => 'Transaction saved.' ) );
        }
    }

    public function delete_transaction() {
        $this->verify();
        $id = intval( $_POST['id'] );
        if ( ! $id ) { $this->err( 'Invalid ID.' ); return; }
        DCT_DB::delete_transaction( $id );
        $this->ok( array( 'message' => 'Transaction deleted.' ) );
    }

    /* ── Summary ── */

    public function get_summary() {
        if ( current_user_can( 'manage_options' ) ) {
            $this->verify();
        } else {
            $this->verify_public();
        }
        $sid = intval( $_POST['stakeholder_id'] );
        $pid = intval( $_POST['project_id'] ?? 0 );
        $date_from = sanitize_text_field( $_POST['date_from'] ?? '' );
        $date_to = sanitize_text_field( $_POST['date_to'] ?? '' );
        if ( ! $sid ) { $this->err( 'Select a stakeholder.' ); return; }
        $sh = DCT_DB::get_stakeholder( $sid );
        $summary = DCT_DB::get_stakeholder_summary( $sid, $pid ?: null, $date_from ?: null, $date_to ?: null );
        $summary['stakeholder'] = $sh;
        $this->ok( $summary );
    }

    public function get_all_summary() {
        if ( current_user_can( 'manage_options' ) ) {
            $this->verify();
        } else {
            $this->verify_public();
        }
        $stakeholders = DCT_DB::get_stakeholders();
        $pid = intval( $_POST['project_id'] ?? 0 );
        $date_from = sanitize_text_field( $_POST['date_from'] ?? '' );
        $date_to = sanitize_text_field( $_POST['date_to'] ?? '' );
        $result = array();
        foreach ( $stakeholders as $s ) {
            $sum = DCT_DB::get_stakeholder_summary( $s->id, $pid ?: null, $date_from ?: null, $date_to ?: null );
            $result[] = array(
                'id'       => $s->id,
                'name'     => $s->name,
                'received' => $sum['received'],
                'given'    => $sum['given'],
                'expenses' => $sum['expenses'],
                'balance'  => $sum['balance'],
            );
        }
        $this->ok( $result );
    }
    public function get_category_costs() {
        error_log( 'get_category_costs called with: ' . print_r( $_POST, true ) );
        // $data = array(
        //     'Design' => 1200,
        //     'Development' => 3500,
        //     'Marketing' => 800,
        // );
        // echo json_encode( $_POST );
        // exit;

        $this->verify();
        $phase = sanitize_text_field( $_POST['phase'] ?? '' );
        $project_id = sanitize_text_field( $_POST['project_id'] ?? '' );
        $date_from = sanitize_text_field( $_POST['date_from'] ?? '' );
        $date_to = sanitize_text_field( $_POST['date_to'] ?? '' );

        if ( empty( $phase ) ) {
            $this->err( 'Phase is required.' );
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'dct_transactions';

        $query = "SELECT category, SUM(amount) as total_cost FROM {$table_name}";
        $query .= " WHERE phase = %s AND transaction_type = 'expense'";
        $args = array( $phase );

        if ( ! empty( $project_id ) ) {
            $query .= " AND project_id = %d";
            $args[] = $project_id;
        }

        if ( ! empty( $date_from ) ) {
            $query .= " AND transaction_date >= %s";
            $args[] = $date_from;
        }
        if ( ! empty( $date_to ) ) {
            $query .= " AND transaction_date <= %s";
            $args[] = $date_to;
        }

        $query .= " GROUP BY category ORDER BY total_cost DESC";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $args ), ARRAY_A );

        $category_costs = array();
        if ( ! empty( $results ) ) {
            foreach ( $results as $row ) {
                // $category_costs[ $row['category'] ] = $row['total_cost'];
                $category_costs[] = array(
                    'category' => $row['category'],
                    'phase' => $phase,
                    'total_cost' => floatval( $row['total_cost'] ),
                );
            }
        }
        
        // echo json_encode( $category_costs );
        // exit;
        $this->ok( $category_costs );
    }

    /* ── Export/Import ── */

    public function export_data() {
        $this->verify();
        $data = DCT_DB::export_data();
        
        $filename = 'daily-cash-tracker-export-' . date( 'Y-m-d-H-i-s' ) . '.json';
        
        header( 'Content-Type: application/json' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        
        echo json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        exit;
    }

    public function import_prepare() {
        try {
            $this->verify();

            if ( empty( $_FILES['import_file'] ) ) {
                $this->err( 'Please select a file to import.' );
                return;
            }

            $file = $_FILES['import_file'];

            if ( $file['error'] !== UPLOAD_ERR_OK ) {
                $this->err( 'File upload error: ' . $file['error'] );
                return;
            }

            $content = file_get_contents( $file['tmp_name'] );
            if ( $content === false ) {
                $this->err( 'Failed to read uploaded file.' );
                return;
            }

            $data = json_decode( $content );

            if ( json_last_error() !== JSON_ERROR_NONE ) {
                $this->err( 'Invalid JSON file: ' . json_last_error_msg() );
                return;
            }

            $session_id = wp_generate_password( 32, false );
            $import_data = array(
                'session_id' => $session_id,
                'projects' => isset( $data->projects ) ? $data->projects : array(),
                'stakeholders' => isset( $data->stakeholders ) ? $data->stakeholders : array(),
                'project_stakeholders' => isset( $data->project_stakeholders ) ? $data->project_stakeholders : array(),
                'transactions' => isset( $data->transactions ) ? $data->transactions : array(),
                'total_rows' => 0,
                'processed_rows' => 0,
                'current_table' => '',
                'import_order' => array( 'projects', 'stakeholders', 'project_stakeholders', 'transactions' ),
                'table_progress' => array(
                    'projects' => array( 'total' => 0, 'processed' => 0 ),
                    'stakeholders' => array( 'total' => 0, 'processed' => 0 ),
                    'project_stakeholders' => array( 'total' => 0, 'processed' => 0 ),
                    'transactions' => array( 'total' => 0, 'processed' => 0 ),
                ),
            );

            foreach ( $import_data['table_progress'] as $table => $counts ) {
                $import_data['table_progress'][ $table ]['total'] = count( $import_data[ $table ] );
                $import_data['total_rows'] += count( $import_data[ $table ] );
            }

            set_transient( 'dct_import_' . $session_id, $import_data, 3600 );

            $this->ok( array(
                'session_id' => $session_id,
                'total_rows' => $import_data['total_rows'],
                'tables' => $import_data['table_progress'],
            ) );
        } catch ( Exception $e ) {
            $this->err( 'Error preparing import: ' . $e->getMessage() );
        }
    }

    public function import_process() {
        try {
            $this->verify();

            $session_id = sanitize_text_field( $_POST['session_id'] ?? '' );
            $chunk_size = intval( $_POST['chunk_size'] ?? 50 );

            if ( empty( $session_id ) ) {
                $this->err( 'Invalid session.' );
                return;
            }

            $import_data = get_transient( 'dct_import_' . $session_id );

            if ( false === $import_data ) {
                $this->err( 'Import session expired. Please start over.' );
                return;
            }

            $results = array();
            $rows_processed = 0;

            foreach ( $import_data['import_order'] as $table ) {
                if ( $import_data['table_progress'][ $table ]['processed'] >= $import_data['table_progress'][ $table ]['total'] ) {
                    continue;
                }

                $import_data['current_table'] = $table;
                $start = $import_data['table_progress'][ $table ]['processed'];
                $end = min( $start + $chunk_size, $import_data['table_progress'][ $table ]['total'] );
                $chunk = array_slice( $import_data[ $table ], $start, $chunk_size );

                $chunk_result = DCT_DB::import_chunk( $table, $chunk );

                $import_data['table_progress'][ $table ]['processed'] = $end;
                $import_data['processed_rows'] += count( $chunk );
                $rows_processed += count( $chunk );

                $results[] = array(
                    'table' => $table,
                    'processed' => $end,
                    'total' => $import_data['table_progress'][ $table ]['total'],
                    'chunk_result' => $chunk_result,
                );

                if ( $rows_processed >= $chunk_size ) {
                    break;
                }
            }

            set_transient( 'dct_import_' . $session_id, $import_data, 3600 );

            $all_complete = true;
            foreach ( $import_data['table_progress'] as $table => $counts ) {
                if ( $counts['processed'] < $counts['total'] ) {
                    $all_complete = false;
                    break;
                }
            }

            $percent = min( 100, round( ( $import_data['processed_rows'] / max( 1, $import_data['total_rows'] ) ) * 100 ) );

            $this->ok( array(
                'complete' => $all_complete,
                'processed_rows' => $import_data['processed_rows'],
                'total_rows' => $import_data['total_rows'],
                'percent' => $percent,
                'current_table' => $import_data['current_table'],
                'results' => $results,
            ) );
        } catch ( Exception $e ) {
            $this->err( 'Error processing import: ' . $e->getMessage() );
        }
    }

    public function import_data() {
        $this->verify();
        
        if ( empty( $_FILES['import_file'] ) ) {
            $this->err( 'Please select a file to import.' );
            return;
        }
        
        $file = $_FILES['import_file'];
        
        if ( $file['error'] !== UPLOAD_ERR_OK ) {
            $this->err( 'File upload error.' );
            return;
        }
        
        if ( $file['type'] !== 'application/json' && $file['type'] !== 'text/plain' && $file['type'] !== '' ) {
            $this->err( 'Please upload a valid JSON file.' );
            return;
        }
        
        $content = file_get_contents( $file['tmp_name'] );
        $data = json_decode( $content );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $this->err( 'Invalid JSON file: ' . json_last_error_msg() );
            return;
        }
        
        $result = DCT_DB::import_data( $data );
        
        if ( $result['success'] ) {
            $this->ok( $result );
        } else {
            $this->err( $result['message'] );
        }
    }
}
