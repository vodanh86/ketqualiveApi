
<?php
    $url = "https://congvi.top/api/user-wallets";
    $ch = curl_init( $url );
    # Setup request to send json via POST.
    $payload = json_encode( array( "uid"=> 7, "api_token" => "6947e182ea6e7aadaf55a5c813e7b2ae" ) );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    # Return response instead of printing.
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $result = json_decode(curl_exec($ch));
    curl_close($ch);
?>
<div class="xs-box xs-box-red">
    <div class="xs-box-head">Nạp tiền</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <div class="max4d-result-g1">
                <div class="max4d-list-digit red bold">
                Vui lòng nạp tiền vào ví Momo
                </div>
            </div>
            <div class="max4d-result-g1">
                <div class="max4d-list-digit red bold">
                <img src="<?=$result->data[0]?>" />
                </div>
            </div>
        </div>
    </div>
</div>