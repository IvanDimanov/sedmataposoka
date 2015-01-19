<?php

function printLayout($this, $templateLayout = null, $mainLayout, $data) {
    //v header-a se zarejda catalog ads baner thought of day 
    $this->load->view('templates/header',$data);
    if(isset($templateLayout))
    {
        $this->load->view( $templateLayout, $data );
    }
    $this->load->view( $mainLayout,$data );
    $this->load->view('templates/footer',$data);
}
?>
