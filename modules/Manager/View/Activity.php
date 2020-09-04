<div class="pg-title user-vip-title"><?php echo __('Danh sách hoạt động') .' ('.$total.')'?></div>
<div class="pg-content user-vip-content">
    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
        <?php if(isset($error)){
            if($error == 1){
                echo '<div class="row">
                    <div class="col-md-12 col-sm-12 col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          Hủy thất bại
                        </div>
                    </div>
                </div>';
            }else{
                echo '<div class="row">
                    <div class="col-md-12 col-sm-12 col-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                          Hủy thành công
                        </div>
                    </div>
                </div>';
            }
        }?>
        <div class="row mb-4">
            <div class="col-md-12 col-sm-12 col-12">
                <table id="datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">ManagerID</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Loại</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Chi tiết</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(isset($activity) && is_array($activity) && count($activity) > 0){
                        $count = 0;
                        foreach($activity as $item){
                            $count++;
                            $action = json_decode($item['activity']); 
                    ?>
                        <tr role="row">
                            <td><?php echo $arrAcc[$item['manager_id']] ?></td>
                            <td><?php echo (isset($arrType[$action->type]) ? $arrType[$action->type] : '') ?></td>
                            <td>
                                <p><?php echo date('H:i, d-m-Y',$item['created_at']) ?></p>
                                <p>UserID: <?php echo $action->user_id ?></p>
                                <p>Tên: <?php echo $action->custom_title ?></p>
                                <?php if(isset($action->num)){ ?>
                                <p>Số ngày: <?php echo $action->num ?></p>
                                <?php } ?>
                                <?php if(isset($action->coin)){ ?>
                                <p>Số coin: <?php echo $action->coin ?></p>
                                <?php } ?>
                                <?php if(isset($action->password)){ ?>
                                <p>Mật khẩu: <?php echo $action->password ?></p>
                                <?php } ?>
                                <?php if(isset($action->day)){ ?>
                                <p>Số ngày khóa: <?php echo $action->day ?></p>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($action->type != 'datlaimatkhau'){ ?>
                                <a href="roll-back-activity?id=<?php echo $item['id']?>"  onclick="return confirm('Hủy hoạt động này ?')">Hủy</a>
                                <?php } ?>
                            </td>
                        </tr>
                        
                     <?php   }
                    }
                    ?>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-6">
                <?php
                    $paginateParams = array();
                    echo Mava_View::buildPaginationBootrap(
                        Mava_Url::buildLink('manager/activity',$paginateParams),
                        ceil($total/$limit),
                        $page,
                        3,
                        'item_list_pagination'
                    );
                ?>
            </div>
        </div>
    </div>
</div>