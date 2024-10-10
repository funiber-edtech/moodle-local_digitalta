<?php

class external_resources_get_used_langs extends external_api {
    public static function resources_get_used_langs_parameters() {
        return new external_function_parameters(
            array()
        );
    }
    public static function resources_get_used_langs()
    {
        global $DB;

        $sql = "SELECT DISTINCT lang FROM {digitalta_resources} WHERE lang IS NOT NULL";
        $langs = array_keys($DB->get_records_sql($sql));
        return $langs;
    }

    public static function resources_get_used_langs_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'lang')
        );
    }
}