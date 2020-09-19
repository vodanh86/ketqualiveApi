
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
    <div class="xs-box-head">Nạp ngân lượng</div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <div class="max4d-result-g1">
                <div class="max4d-list-digit red bold">
                Vui lòng nạp tiền vào ví Momo <br/> ( Bạn cần cập nhật số điện thoại của tài khoản trước khi chuyển tiền qua Momo)
                </div>
            </div>
            <div class="max4d-result-g1">
                <div class="max4d-list-digit red bold">
                <img src="<?=$result->data[0]?>" style="width:100%"/>
                </div>
            </div>
        </div>
    </div>
    <div class="xs-box-body">
        <div id="xs_stats_result">
            <div class="max4d-result-g1">
                <div class="max4d-list-digit bold">
                    <div class="red">
                    Nạp tiền qua thẻ cào 
                    <?php if (!Mava_Visitor::getInstance()->get('token')) { ?>
                    <br/> ( Vui lòng đăng nhập để nạp ngân lượng)
                    <?php } ?>
                    </div>
                    <?php if (Mava_Visitor::getInstance()->get('token')) { ?>
                    <div class="row">
                    &nbsp;&nbsp;&nbsp;&nbsp;Vui lòng chọn loại thẻ:
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="#" onclick="return openTopupDiaglog('VTT');">
                            <img src="data/images/branch/viettel.png" width="100%"/>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" onclick="return openTopupDiaglog('VMS');">
                            <img src="data/images/branch/mobifone.png" width="100%"/>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" onclick="return openTopupDiaglog('VINA');">
                            <img src="data/images/branch/vinaphone.png" width="100%"/>
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="max4d-result-g1">
                <div class="max4d-list-digit bold">
                    <div class="row">
                        <div class="max4d-list-digit red bold">
                        Bảng giá ngân lượng
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        20.000 VND
                          </div>
                        <div class="col-md-6">
                            3.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div > 
                    <hr style="margin: 10px"/>
                    <div class="row">
                        <div class="col-md-6">
                        50.000 VND
                          </div>
                        <div class="col-md-6">
                            9.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div>
                    <hr style="margin: 10px"/> 
                    <div class="row">
                        <div class="col-md-6">
                        100.000 VND
                          </div>
                        <div class="col-md-6">
                            20.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div>
                    <hr style="margin: 10px"/> 
                    <div class="row">
                        <div class="col-md-6">
                        200.000 VND
                          </div>
                        <div class="col-md-6">
                            45.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div > 
                    <hr style="margin: 10px"/>
                    <div class="row">
                        <div class="col-md-6">
                        200.000 VND
                          </div>
                        <div class="col-md-6">
                            70.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div>
                    <hr style="margin: 10px"/> 
                    <div class="row">
                        <div class="col-md-6">
                        500.000 VND
                          </div>
                        <div class="col-md-6">
                            150.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div>
                    <hr style="margin: 10px"/> 
                    <div class="row">
                        <div class="col-md-6">
                        1.000.000 VND
                          </div>
                        <div class="col-md-6">
                            350.000
                            <img src="data/images/coin.png" width="25px"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="mbd_modal_create_reference_url">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo __('close'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Nạp ngân lượng</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Seri thẻ</label>
                    <input type="text" class="form-control" id="topup_serial" placeholder="" />
                </div>
                <div class="form-group">
                    <label>Mã số thẻ</label>
                    <input type="text" class="form-control" id="topup_number" placeholder="" />
                 </div>
                 <div class="form-group">
                    <label>Mệnh giá thẻ</label>
                    <select class="form-control form-control-sm" id="topup_value">
                    <option value="20000">20.000 VND</option>
                    <option value="50000">50.000 VND</option>
                    <option value="100000">100.000 VND</option>
                    <option value="200000">200.000 VND</option>
                    <option value="300000">300.000 VND</option>
                    <option value="500000">500.000 VND</option>
                    <option value="1000000">1.000.000 VND</option>
                    </select>
                    </div>
                
                <span class="red page_notice" id="topup_notice"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="mbd_submit_create_url"  onClick="userCharge()">Nạp </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var selectedBranch = "VTT";
    function openTopupDiaglog(branch){
        $('#mbd_modal_create_reference_url').modal('show');
        $("#topup_notice").html("");
        selectedBranch = branch;
        return false;
    };
    function userCharge(){
        MV.post(DOMAIN +'/api/user/charge', {
                telcoId: selectedBranch,
                card_number: $('#topup_number').val(),
                card_serial: $('#topup_serial').val(),
                card_value: $('#topup_value').val(),
                token: '<?php echo Mava_Visitor::getInstance()->get('token'); ?>'
            }, function(res){
                if (res.error === 0){
                    checkChargeStatus(res.data.code);
                } else {
                    $("#topup_notice").html(res.message);
                }  
            });
    }
    function checkChargeStatus(code) {
        $("#topup_notice").html("Vui lòng không tắt cửa sổ này...");
        MV.post(DOMAIN +'/api/user/check-card-status', {
                code: code,
                token: '<?php echo Mava_Visitor::getInstance()->get('token'); ?>'
            }, function(res){
                if (res.error === 0){
                    if(result.data.user_id > 0){
                        $("#topup_notice").html("Nạp thẻ thành công");
                        //$('#mbd_modal_create_reference_url').modal('hide');
                        /*AsyncStorage.removeItem('charge_code');
                        this.props.open_custom_modal("Thành công", result.message, "OK", "", this.props.navigation);
                        this.alertPopup.close();
                        this.props.updateUserInfo(format_user_data(result.data, true));*/
                    }else{
                        setTimeout(async () => {
                            checkChargeStatus(code);
                        }, 5000);
                    }
                } else {
                    $("#topup_notice").html(res.message);
                }  
            });
    };
</script>