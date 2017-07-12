<?php

namespace LePlugin\Core;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
  @copyright Les Coders
 * An abstract class that uses wpdb for querying.
 * wpdb methods are protected and final to be used only inside of implementing class and to avoid overrides.
 * This is to have strict implementation of descriptive methods.
 * Example: getProductById($id) or add($productName,$productDescription);
 * Repositories methods should be aware only of database properties, not Entities
 * Example: add($productName,$productDescription) not add($productObj);
 */
abstract class WpdbRepository {

    /** @property \wpdb $wpdb */
    protected $wpdb;
    protected $table_name;

    public function __construct($table_name_without_prefix) {
        global $wpdb, $table_prefix;
        $this->wpdb = $wpdb;
        $this->table_name = $table_prefix . $table_name_without_prefix;
    }

    public function getTableName() {
        return $this->table_name;
    }

    protected final function wp_prepare($query, $args) {
        return $this->wpdb->prepare($query, $args);
    }

    protected final function wp_get_var($query = null, $x = 0, $y = 0) {
        return $this->wpdb->get_var($query, $x, $y);
    }

    protected final function wp_get_row($query = null, $output = OBJECT, $y = 0) {
        return $this->wpdb->get_row($query, $output, $y);
    }

    protected final function wp_get_col($query = null, $x = 0) {
        return $this->wpdb->get_col($query, $x);
    }

    protected final function wp_get_results($query, $output = OBJECT) {
        return $this->wpdb->get_results($query, $output);
    }

    protected final function wp_insert($data, $format = null) {
        $result = $this->wpdb->insert($this->table_name, $data, $format);
        if ($result == 1) {
            return $this->wpdb->insert_id;
        }
        return $result;
    }

    protected final function wp_update($data, $where, $format = null, $where_format = null) {
        return $this->wpdb->update($this->table_name, $data, $where, $format, $where_format);
    }

    protected final function wp_delete($where, $where_format = null) {
        return $this->wpdb->delete($this->table_name, $where, $where_format);
    }

    protected final function wp_query($query) {
        return $this->wpdb->query($query);
    }

    public function countAll() {
        return $this->wp_get_var("SELECT count(1) FROM $this->table_name");
    }

}
