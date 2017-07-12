<?php

namespace LePlugin\Core;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
interface RepositoryInterface {

    function getTableName();

    function prepare($query, $args);

    function get_var($query = null, $x = 0, $y = 0);

    function get_row($query = null, $output = OBJECT, $y = 0);

    function get_col($query = null, $x = 0);

    function get_results($query, $output = OBJECT);

    function insert($data, $format = null);

    function update($data, $where, $format = null, $where_format = null);

    function delete($where, $where_format = null);

    function query($query);
}
