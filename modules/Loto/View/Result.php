<?php
    if($start_time && $end_time){
        echo get_result_html(date('d-m-Y', $start_time), date('d-m-Y', $end_time), $province, true);
    }else{
        echo get_result_html('', '', $province, true);
    }

?>