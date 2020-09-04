<?php if($messageError != ''){
    echo '<div class="xs-box">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$messageError.'
        </div>
    </div>';
} ?>
<div class="xs-box xs-box-red">
    <div class="xs-box-head">Kết quả Vietlot Power 6/55 (10 ngày gần nhất)</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <?php
                $html = '';
                if(isset($result) && count($result) > 0){
                    if(isset($result['latest']) && count($result['latest']) > 0){
                        $html .= '<div class="result-latest-block">
                            <p class="jackpot-mega-next-date bold">Ngày quay thưởng '.(isset($result['latest']['next_time']) ? $result['latest']['next_time'] : '').'</p>
                            <p class="jackpot-power-1-title bold">Jackpot 1</p>
                            <p class="jackpot-power-1-result red bold">'.(isset($result['latest']['jackpot_1']) ? $result['latest']['jackpot_1'] : '').'Đ</p>
                            <p class="jackpot-power-2-title bold">Jackpot 2</p>
                            <p class="jackpot-power-2-result red bold">'.(isset($result['latest']['jackpot_2']) ? $result['latest']['jackpot_2'] : '').'Đ</p>
                        </div>';
                    }
                    if(isset($result['items']) && count($result['items']) > 0){
                        foreach ($result['items'] as $item) {
                            $html .= '<div class="result-item-block">
                                <p class="power-code">Kì quay thưởng '.(isset($item['code']) ? $item['code'] : '').'</p>
                                <p class="power-date">Ngày quay thưởng '.(isset($item['result_time']) ? $item['result_time'] : '').'</p>
                                <p class="red bold power-number">
                                    <span class="number-result">'.(isset($item['num_1']) ? $item['num_1'] : '').'</span>
                                    <span class="number-result">'.(isset($item['num_2']) ? $item['num_2'] : '').'</span>
                                    <span class="number-result">'.(isset($item['num_3']) ? $item['num_3'] : '').'</span>
                                    <span class="number-result">'.(isset($item['num_4']) ? $item['num_4'] : '').'</span>
                                    <span class="number-result">'.(isset($item['num_5']) ? $item['num_5'] : '').'</span>
                                    <span class="number-result">'.(isset($item['num_6']) ? $item['num_6'] : '').'</span>
                                    <span class="line-space-number"></span>
                                    <span class="number-result">'.(isset($item['num_7']) ? $item['num_7'] : '').'</span>
                                </p>
                            </div>';
                        }
                    }                  
                }
                echo $html;
            ?>
        </div>
    </div>
</div>