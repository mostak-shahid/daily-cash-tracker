<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DCT_DB {

    public static function install() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        $sql = array();

        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dct_projects (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            address TEXT,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dct_stakeholders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            address TEXT,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dct_project_stakeholders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id BIGINT UNSIGNED NOT NULL,
            stakeholder_id BIGINT UNSIGNED NOT NULL,
            UNIQUE KEY unique_assignment (project_id, stakeholder_id)
        ) $charset;";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dct_transactions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id BIGINT UNSIGNED NOT NULL,
            from_stakeholder_id BIGINT UNSIGNED,
            to_stakeholder_id BIGINT UNSIGNED,
            transaction_type ENUM('transfer','expense') NOT NULL DEFAULT 'transfer',
            category VARCHAR(100),
            phase VARCHAR(100),
            amount DECIMAL(15,2) NOT NULL,
            description TEXT,
            transaction_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ( $sql as $s ) {
            dbDelta( $s );
        }
    }

    /* ── Projects ── */

    public static function get_projects() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}dct_projects ORDER BY name ASC" );
    }

    public static function get_project( $id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dct_projects WHERE id=%d", $id ) );
    }

    public static function insert_project( $data ) {
        global $wpdb;
        $wpdb->insert( "{$wpdb->prefix}dct_projects", array(
            'name'        => sanitize_text_field( $data['name'] ),
            'address'     => sanitize_textarea_field( $data['address'] ),
            'description' => sanitize_textarea_field( $data['description'] ),
        ) );
        return $wpdb->insert_id;
    }

    public static function update_project( $id, $data ) {
        global $wpdb;
        return $wpdb->update( "{$wpdb->prefix}dct_projects", array(
            'name'        => sanitize_text_field( $data['name'] ),
            'address'     => sanitize_textarea_field( $data['address'] ),
            'description' => sanitize_textarea_field( $data['description'] ),
        ), array( 'id' => $id ) );
    }

    public static function delete_project( $id ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}dct_project_stakeholders", array( 'project_id' => $id ) );
        $wpdb->delete( "{$wpdb->prefix}dct_transactions", array( 'project_id' => $id ) );
        return $wpdb->delete( "{$wpdb->prefix}dct_projects", array( 'id' => $id ) );
    }

    /* ── Stakeholders ── */

    public static function get_stakeholders() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}dct_stakeholders ORDER BY name ASC" );
    }

    public static function get_stakeholder( $id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dct_stakeholders WHERE id=%d", $id ) );
    }

    public static function insert_stakeholder( $data ) {
        global $wpdb;
        $wpdb->insert( "{$wpdb->prefix}dct_stakeholders", array(
            'name'        => sanitize_text_field( $data['name'] ),
            'phone'       => sanitize_text_field( $data['phone'] ),
            'address'     => sanitize_textarea_field( $data['address'] ),
            'description' => sanitize_textarea_field( $data['description'] ),
        ) );
        return $wpdb->insert_id;
    }

    public static function update_stakeholder( $id, $data ) {
        global $wpdb;
        return $wpdb->update( "{$wpdb->prefix}dct_stakeholders", array(
            'name'        => sanitize_text_field( $data['name'] ),
            'phone'       => sanitize_text_field( $data['phone'] ),
            'address'     => sanitize_textarea_field( $data['address'] ),
            'description' => sanitize_textarea_field( $data['description'] ),
        ), array( 'id' => $id ) );
    }

    public static function delete_stakeholder( $id ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}dct_project_stakeholders", array( 'stakeholder_id' => $id ) );
        return $wpdb->delete( "{$wpdb->prefix}dct_stakeholders", array( 'id' => $id ) );
    }

    /* ── Assignments ── */

    public static function get_project_stakeholders( $project_id ) {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT s.* FROM {$wpdb->prefix}dct_stakeholders s
             INNER JOIN {$wpdb->prefix}dct_project_stakeholders ps ON ps.stakeholder_id = s.id
             WHERE ps.project_id = %d ORDER BY s.name ASC",
            $project_id
        ) );
    }

    public static function assign_stakeholder( $project_id, $stakeholder_id ) {
        global $wpdb;
        $wpdb->replace( "{$wpdb->prefix}dct_project_stakeholders", array(
            'project_id'     => $project_id,
            'stakeholder_id' => $stakeholder_id,
        ) );
        return $wpdb->insert_id;
    }

    public static function unassign_stakeholder( $project_id, $stakeholder_id ) {
        global $wpdb;
        return $wpdb->delete( "{$wpdb->prefix}dct_project_stakeholders", array(
            'project_id'     => $project_id,
            'stakeholder_id' => $stakeholder_id,
        ) );
    }

    /* ── Transactions ── */

    public static function get_transactions( $filters = array() ) {
        global $wpdb;
        $where = array( '1=1' );
        $args  = array();

        if ( ! empty( $filters['project_id'] ) ) {
            $where[] = 't.project_id = %d';
            $args[]  = $filters['project_id'];
        }
        if ( ! empty( $filters['stakeholder_id'] ) ) {
            $where[] = '(t.from_stakeholder_id = %d OR t.to_stakeholder_id = %d)';
            $args[]  = $filters['stakeholder_id'];
            $args[]  = $filters['stakeholder_id'];
        }
        if ( ! empty( $filters['transaction_type'] ) ) {
            $where[] = 't.transaction_type = %s';
            $args[]  = $filters['transaction_type'];
        }
        if ( ! empty( $filters['from_stakeholder_id'] ) ) {
            $where[] = 't.from_stakeholder_id = %d';
            $args[]  = $filters['from_stakeholder_id'];
        }
        if ( ! empty( $filters['to_stakeholder_id'] ) ) {
            $where[] = 't.to_stakeholder_id = %d';
            $args[]  = $filters['to_stakeholder_id'];
        }
        if ( ! empty( $filters['category'] ) ) {
            $where[] = 't.category = %s';
            $args[]  = $filters['category'];
        }
        if ( ! empty( $filters['phase'] ) ) {
            $where[] = 't.phase = %s';
            $args[]  = $filters['phase'];
        }
        if ( ! empty( $filters['date_from'] ) ) {
            $where[] = 't.transaction_date >= %s';
            $args[]  = $filters['date_from'];
        }
        if ( ! empty( $filters['date_to'] ) ) {
            $where[] = 't.transaction_date <= %s';
            $args[]  = $filters['date_to'];
        }

        $where_str = implode( ' AND ', $where );
        $sql = "SELECT t.*,
                    p.name AS project_name,
                    fs.name AS from_name,
                    ts.name AS to_name
                FROM {$wpdb->prefix}dct_transactions t
                LEFT JOIN {$wpdb->prefix}dct_projects p ON p.id = t.project_id
                LEFT JOIN {$wpdb->prefix}dct_stakeholders fs ON fs.id = t.from_stakeholder_id
                LEFT JOIN {$wpdb->prefix}dct_stakeholders ts ON ts.id = t.to_stakeholder_id
                WHERE $where_str
                ORDER BY t.transaction_date DESC, t.id DESC";

        if ( $args ) {
            $sql = $wpdb->prepare( $sql, $args );
        }
        return $wpdb->get_results( $sql );
    }

    public static function insert_transaction( $data ) {
        global $wpdb;
        $wpdb->insert( "{$wpdb->prefix}dct_transactions", array(
            'project_id'          => intval( $data['project_id'] ),
            'from_stakeholder_id' => ! empty( $data['from_stakeholder_id'] ) ? intval( $data['from_stakeholder_id'] ) : null,
            'to_stakeholder_id'   => ! empty( $data['to_stakeholder_id'] )   ? intval( $data['to_stakeholder_id'] )   : null,
            'transaction_type'    => sanitize_text_field( $data['transaction_type'] ),
            'category'            => sanitize_text_field( $data['category'] ),
            'phase'               => sanitize_text_field( $data['phase'] ),
            'amount'              => floatval( $data['amount'] ),
            'description'         => sanitize_textarea_field( $data['description'] ),
            'transaction_date'    => sanitize_text_field( $data['transaction_date'] ),
        ), array( '%d', '%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s' ) );
        return $wpdb->insert_id;
    }

    public static function delete_transaction( $id ) {
        global $wpdb;
        return $wpdb->delete( "{$wpdb->prefix}dct_transactions", array( 'id' => $id ) );
    }

    public static function update_transaction( $id, $data ) {
        global $wpdb;
        return $wpdb->update( "{$wpdb->prefix}dct_transactions", array(
            'project_id'          => intval( $data['project_id'] ),
            'from_stakeholder_id' => ! empty( $data['from_stakeholder_id'] ) ? intval( $data['from_stakeholder_id'] ) : null,
            'to_stakeholder_id'   => ! empty( $data['to_stakeholder_id'] )   ? intval( $data['to_stakeholder_id'] )   : null,
            'transaction_type'    => sanitize_text_field( $data['transaction_type'] ),
            'category'            => sanitize_text_field( $data['category'] ),
            'phase'               => sanitize_text_field( $data['phase'] ),
            'amount'              => floatval( $data['amount'] ),
            'description'         => sanitize_textarea_field( $data['description'] ),
            'transaction_date'    => sanitize_text_field( $data['transaction_date'] ),
        ), array( 'id' => $id ) );
    }

    /* ── Summary ── */

    public static function get_stakeholder_summary( $stakeholder_id, $project_id = null, $date_from = null, $date_to = null ) {
        global $wpdb;
        $project_cond = $project_id ? $wpdb->prepare( 'AND t.project_id = %d', $project_id ) : '';
        $date_conditions = array();
        $date_args = array();

        if ( $date_from ) {
            $date_conditions[] = 't.transaction_date >= %s';
            $date_args[] = $date_from;
        }
        if ( $date_to ) {
            $date_conditions[] = 't.transaction_date <= %s';
            $date_args[] = $date_to;
        }
        $date_cond = $date_conditions ? 'AND ' . implode( ' AND ', $date_conditions ) : '';

        // Money received (someone gave to this stakeholder)
        $received_sql = $wpdb->prepare(
            "SELECT COALESCE(SUM(amount),0) FROM {$wpdb->prefix}dct_transactions t
             WHERE t.to_stakeholder_id = %d AND t.transaction_type='transfer' $project_cond $date_cond",
            $stakeholder_id,
            ...$date_args
        );
        $received = $wpdb->get_var( $received_sql );

        // Money given out (this stakeholder gave to someone)
        $given_sql = $wpdb->prepare(
            "SELECT COALESCE(SUM(amount),0) FROM {$wpdb->prefix}dct_transactions t
             WHERE t.from_stakeholder_id = %d AND t.transaction_type='transfer' $project_cond $date_cond",
            $stakeholder_id,
            ...$date_args
        );
        $given = $wpdb->get_var( $given_sql );

        // Expenses paid by this stakeholder
        $expenses_sql = $wpdb->prepare(
            "SELECT COALESCE(SUM(amount),0) FROM {$wpdb->prefix}dct_transactions t
             WHERE t.from_stakeholder_id = %d AND t.transaction_type='expense' $project_cond $date_cond",
            $stakeholder_id,
            ...$date_args
        );
        $expenses = $wpdb->get_var( $expenses_sql );

        // Recent transactions involving this stakeholder
        $project_cond2 = $project_id ? $wpdb->prepare( 'AND t.project_id = %d', $project_id ) : '';
        $transactions_sql = $wpdb->prepare(
            "SELECT t.*, p.name AS project_name, fs.name AS from_name, ts.name AS to_name
             FROM {$wpdb->prefix}dct_transactions t
             LEFT JOIN {$wpdb->prefix}dct_projects p ON p.id = t.project_id
             LEFT JOIN {$wpdb->prefix}dct_stakeholders fs ON fs.id = t.from_stakeholder_id
             LEFT JOIN {$wpdb->prefix}dct_stakeholders ts ON ts.id = t.to_stakeholder_id
             WHERE (t.from_stakeholder_id = %d OR t.to_stakeholder_id = %d) $project_cond2 $date_cond
             ORDER BY t.transaction_date DESC, t.id DESC LIMIT 100",
            $stakeholder_id,
            $stakeholder_id,
            ...$date_args
        );
        $transactions = $wpdb->get_results( $transactions_sql );

        return array(
            'received'     => floatval( $received ),
            'given'        => floatval( $given ),
            'expenses'     => floatval( $expenses ),
            'balance'      => floatval( $received ) - floatval( $given ) - floatval( $expenses ),
            'transactions' => $transactions,
        );
    }
}
