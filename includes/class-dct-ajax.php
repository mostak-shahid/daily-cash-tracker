<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DCT_Ajax {

    public function init() {
        $actions = array(
            'dct_get_projects', 'dct_save_project', 'dct_delete_project',
            'dct_get_stakeholders', 'dct_save_stakeholder', 'dct_delete_stakeholder',
            'dct_get_project_stakeholders', 'dct_assign_stakeholder', 'dct_unassign_stakeholder',
            'dct_get_transactions', 'dct_save_transaction', 'dct_delete_transaction',
            'dct_get_summary', 'dct_get_all_summary',
        );
        foreach ( $actions as $action ) {
            add_action( "wp_ajax_{$action}", array( $this, str_replace( 'dct_', '', $action ) ) );
        }
    }

    private function verify() {
        if ( ! check_ajax_referer( 'dct_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized', 403 );
        }
    }

    private function ok( $data = array() )  { wp_send_json_success( $data ); }
    private function err( $msg )            { wp_send_json_error( $msg ); }

    /* ── Projects ── */

    public function get_projects() {
        $this->verify();
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
        $this->verify();
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
        $this->verify();
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
        $this->verify();
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
}
