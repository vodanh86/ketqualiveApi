<div id="profile_page" class="container">
    <div class="row">
        <?php echo Mava_View::getView('Profile_View_Includes_Menu'); ?>
        <div id="profile_right" class="col-md-9">
            <div class="rm-box-border">
                <div class="clearfix">
                    <div class="pull-left">
                        <h4><?php echo __('consignee_address'); ?></h4>
                    </div>
                    <div class="pull-right">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="DH.address.add(this);"><i class="fa fa-plus"></i> <?php echo __('add_address'); ?></a>
                    </div>
                </div>
                <div class="rm-address-list">
                    <?php
                    if(isset($address) && is_array($address) && count($address) > 0){
                        foreach($address as $item){
                            echo '<div class="rm-address-item"><div class="rm-address-item-inner">
                                        <p><b>'. htmlspecialchars($item['fullname']) .'</b></p>
                                        <p>'. __('phone') .': '.htmlspecialchars($item['phone']) .'</p>
                                        <p>'. __('address') .': '. htmlspecialchars($item['address']) .'</p>
                                        '. ($item['is_default']=='yes'?'<span class="label label-success rm-address-default-label">'. __('default') .'</span>':'') .'
                                        <a href="javascript:void(0);" class="btn btn-default btn-sm" onclick="DH.address.edit(this,'. $item['id'] .');" data-fullname="'. htmlspecialchars($item['fullname']) .'" data-address="'. htmlspecialchars($item['address']) .'" data-phone="'. htmlspecialchars($item['phone']) .'"data-default="'. htmlspecialchars($item['is_default']) .'"><i class="fa fa-edit"></i> '. __('edit') .'</a>
                                        <a href="javascript:void(0);" class="btn btn-default btn-sm" onclick="DH.address.remove(this,'. $item['id'] .');"><i class="fa fa-trash"></i> '. __('delete') .'</a>
                                        '. ($item['is_default'] == 'no'?'<a href="javascript:void(0);" class="btn btn-default btn-sm"  onclick="DH.address.set_default(this,'. $item['id'] .');" data-toggle="tooltip" title="'. __('set_default_address_tooltip') .'">'. __('set_default') .'</a>':'') .'
                                    </div></div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>