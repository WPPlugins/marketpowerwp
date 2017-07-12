<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Core;

/**
 *
 * @property $wpdb \wpdb
 */
abstract class WpdbModel {

    protected $table_name; //should be overridden by implementing class
    protected $primary_key = "id"; //may be overridden by implementing class
    protected $incrementing = true;
    protected $timestamp_column = "date_created"; //automatically sets to time() if not set
    protected $columns = array(); //database columns, should be overridden
    protected $values = array(); //where values are stored
    protected $wpdb;
    private $table_prefix; //making private to avoid unintended assignment

    public function __construct() {
        global $wpdb;
        global $table_prefix;
        $this->wpdb = &$wpdb;
        $this->table_prefix = $table_prefix;
    }

    //getter for column values only
    public function __get($name) {
        if (in_array($name, $this->columns) && isset($this->values[$name])) {
            return $this->values[$name];
        }
        return;
    }

    //setter for column values only
    public function __set($name, $value) {
        if (in_array($name, $this->columns)) {
            $this->values[$name] = $value;
        }
    }

    public function save() {
        //insert or update the values and set primary key
        if (isset($this->values[$this->primary_key])) {
            $where = [$this->primary_key => $this->values[$this->primary_key]];
            $this->wpdb->update($this->table_prefix . $this->table_name, $this->values, $where);
        } else {
            if (!isset($this->values[$this->timestamp_column])) {
                $this->values[$this->timestamp_column] = time();
            }
            $this->wpdb->insert($this->table_prefix . $this->table_name, $this->values);
            if ($this->incrementing) {
                $this->values[$this->primary_key] = $this->wpdb->insert_id;
            }
        }
    }

    public function get($id) {
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name} WHERE {$this->primary_key}=%d";
        $statement = $this->wpdb->prepare($sql, $id);
        return $this->wpdb->get_row($statement);
    }

    public function get_all() {
        $sql = "SELECT * FROM {$this->table_prefix}{$this->table_name}";
        return $this->wpdb->get_row($sql);
    }

    public static function find($id) {
        $instance = new static();
        return $instance->get($id);
    }

    public static function all() {
        $instance = new static();
        return $instance->get_all();
    }

}
