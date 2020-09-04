<?php if($messageError != ''){
    echo '<div class="xs-box">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$messageError.'
        </div>
    </div>';
} ?>
<div class="xs-box xs-box-red">
    <div class="xs-box-head">Kết quả Vietlot Max 4D</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <?php
                $html = '';
                if(isset($result) && count($result) > 0){
                    $html .= '<div class="max4d-title-block">
                        <div class="max4d-prev"><a href="'.($result['id'] > $minId ? 'ket-qua-vietlott-max4d-prev-'.$result['id'] : '#' ).'"><i class="fa fa-angle-left"></i></a></div>
                        <div class="max4d-title">
                            <p class="max4d-code bold">Kì quay thưởng '.(isset($result['code']) ? $result['code'] : '').'</p>
                            <p class="max4d-date bold">Ngày quay thưởng '.(isset($result['result_time']) ? $result['result_time'] : '').'</p>
                        </div>
                        <div class="max4d-next"><a href="'.($result['id'] < $maxId ? 'ket-qua-vietlott-max4d-next-'.$result['id'] : '#' ).'"><i class="fa fa-angle-right"></a></i></div>
                    </div>
                    <div class="max4d-result-block">';
                        if(isset($result['g1'])) {
                            $html .= '<div class="max4d-result-g1">
                                <div class="g1-title red">G1</div>
                                <div class="max4d-list-digit red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g1'][0]) ? $result['g1'][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g1'][1]) ? $result['g1'][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g1'][2]) ? $result['g1'][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g1'][3]) ? $result['g1'][3] : '').'</span>
                                </div>
                            </div>';
                        }
                        if(isset($result['g2'])) {
                            $html .= '<div class="max4d-result-g2">
                                <div class="g2-title red">G2</div>
                                <div class="max4d-result-g20 red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g2'][0][0]) ? $result['g2'][0][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][0][1]) ? $result['g2'][0][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][0][2]) ? $result['g2'][0][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][0][3]) ? $result['g2'][0][3] : '').'</span>
                                </div>
                                <div class="max4d-result-g21 red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g2'][1][0]) ? $result['g2'][1][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][1][1]) ? $result['g2'][1][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][1][2]) ? $result['g2'][1][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g2'][1][3]) ? $result['g2'][1][3] : '').'</span>
                                </div>
                            </div>';
                        }
                        if(isset($result['g3'])) {
                            $html .= '<div class="max4d-result-g3">
                                <div class="g3-title red">G3</div>
                                <div class="max4d-result-g30 red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g3'][0][0]) ? $result['g3'][0][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][0][1]) ? $result['g3'][0][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][0][2]) ? $result['g3'][0][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][0][3]) ? $result['g3'][0][3] : '').'</span>
                                </div>
                                <div class="max4d-result-g31 red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g3'][1][0]) ? $result['g3'][1][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][1][1]) ? $result['g3'][1][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][1][2]) ? $result['g3'][1][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][1][3]) ? $result['g3'][1][3] : '').'</span>
                                </div>
                                <div class="max4d-result-g32 red bold">
                                    <span class="max4d-result-digit">'.(isset($result['g3'][2][0]) ? $result['g3'][2][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][2][1]) ? $result['g3'][2][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][2][2]) ? $result['g3'][2][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['g3'][2][3]) ? $result['g3'][2][3] : '').'</span>
                                </div>
                            </div>';
                        }
                        if(isset($result['kk1'])) {
                            $html .= '<div class="max4d-result-kk1">
                                <div class="kk1-title red">KK1</div>
                                <div class="max4d-list-digit red bold">
                                    <span class="max4d-result-digit">'.(isset($result['kk1'][0]) ? $result['kk1'][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk1'][1]) ? $result['kk1'][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk1'][2]) ? $result['kk1'][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk1'][3]) ? $result['kk1'][3] : '').'</span>
                                </div>
                            </div>';
                        }
                        if(isset($result['kk2'])) {
                            $html .= '<div class="max4d-result-kk2">
                                <div class="kk2-title red">KK2</div>
                                <div class="max4d-list-digit red bold">
                                    <span class="max4d-result-digit">'.(isset($result['kk2'][0]) ? $result['kk2'][0] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk2'][1]) ? $result['kk2'][1] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk2'][2]) ? $result['kk2'][2] : '').'</span>
                                    <span class="max4d-result-digit">'.(isset($result['kk2'][3]) ? $result['kk2'][3] : '').'</span>
                                </div>
                            </div>';
                        }
                                                
                    $html .= '</div>';
                }
                echo $html;
            ?>
        </div>
    </div>
</div>