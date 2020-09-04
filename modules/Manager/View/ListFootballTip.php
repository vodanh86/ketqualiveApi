<div class="pg-title user-vip-title"><?php echo __('Danh sách tip bóng đá') .' ('.$total.')'?></div>
<div class="pg-content user-vip-content">
    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
        <div class="row justify-content-end">
            <div class="col-md-6 col-sm-6 col-12 mb-2 text-right">
                <a href="list-match-day"><button type="button" class="btn btn-secondary ml-2 mb-2">Nhập kèo</button></a>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12 col-sm-12 col-12">
                <table id="datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Gói</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Ngày</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Tip</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Số đăng kí</th>
                    </tr>
                    </thead>
                    <tbody class="football-tip-content">
                    <?php
                    if(isset($tips) && is_array($tips) && count($tips) > 0){
                        foreach($tips as $item){
                            $tip = json_decode($item['tip'], true);
                            ?>
                            <tr role="row">
                                <td><?php echo $item['pack'] ?></td>
                                <td><?php echo $item['tip_date'] ?></td>
                                <td>
                                    <?php foreach ($tip as $v){ ?>
                                        <p class="football-tip-detail">
                                            <span>Thời gian : <?php echo $v['time'] ?></span><br>
                                            <span><?php echo $v['home'] ?> vs <?php echo $v['away'] ?></span><br>
                                            <span><?php echo $v['taixiu'] ?>: <?php echo $v['num'] ?></span><br>
                                            <span>Tỉ số dự đoán : <?php echo $v['ft'] ?></span>
                                        </p>
                                    <?php } ?>
                                </td>
                                <td><?php echo $item['reg_count'] ?></td>
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
                    Mava_Url::buildLink('manager/list-football-tip',$paginateParams),
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