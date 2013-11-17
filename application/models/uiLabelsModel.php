<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Purpose of this class is to communication with the 'ui_labels' table
 * - that's the table responsible for all general site information labels, structured per language
 */
class uiLabelsModel extends CI_Model {

    /*Will return an array of all currently saved UI Labels for the specific incoming '$language'*/
    function getLabelsForLanguage($language) {
        $this->db->select('json_labels');
        $this->db->where('language = "'.$language.'"');
        $query = $this->db->get('ui_labels');

        $json_labels_string = $query->result_array();
        $json_labels_string = $json_labels_string[0]['json_labels'];

        return json_decode( $json_labels_string, true );
    }
}