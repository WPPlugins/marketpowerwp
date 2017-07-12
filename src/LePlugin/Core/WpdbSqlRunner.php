<?php

/**
 * Runs plugin SQLs
 */

namespace LePlugin\Core;

class WpdbSqlRunner {

    private $wpdb;
    private $table_prefix;
    private $wp_error;
    private $sql_parser;

    public function __construct() {
        global $wpdb;
        global $table_prefix;
        global $wp_error;

        $this->wpdb = &$wpdb;
        $this->wp_error = &$wp_error;
        $this->table_prefix = $table_prefix;
        $this->sql_parser = new SqlFileParser();
    }

    /**
     * 
     * @param type $filename - should be full path
     * @throws Exception
     */
    public function execute($filename) {
        //check if file exists
        if (!file_exists($filename)) {
            throw new \Exception("File $filename does not exist!");
        }
        $sql_queries = $this->sql_parser->parse($filename);
        $commit = false;
        $error = false;
        if (count($sql_queries) > 0) {
            $this->wpdb->query('START TRANSACTION');
            $commit = true;
        }
        foreach ($sql_queries as $query) {
            $query = trim($query);
            $query = str_replace('wp_', $this->table_prefix, $query);
            if (false === $this->wpdb->query($query)) {
                if ($this->wp_error) {
                    $error = "Error running query: \n" . $query . "\n" . $this->wpdb->last_error . "\nFile: " . $filename;
                } else {
                    $error = "Error running query: \n" . $query . "\nFile: " . $filename;
                }
                $commit = false;
                break;
            }
        }
        if ($commit) {
            $this->wpdb->query('COMMIT');
        } else if (!$commit && $error) {
            $this->wpdb->query('ROLLBACK');
            throw new \Exception($error);
        }
    }

}
