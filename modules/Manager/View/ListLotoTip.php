<div class="pg-title user-vip-title"><?php echo __('Danh sách tip lô tô') .' ('.$total.')'?></div>
<div class="pg-content user-vip-content">
    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
        <div class="row justify-content-end">
            <div class="col-md-6 col-sm-6 col-12 mb-2 text-right">
                <a href="loto-tip"><button type="button" class="btn btn-secondary ml-2 mb-2">Thêm Tip</button></a>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12 col-sm-12 col-12">
                <table id="datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                    <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Gói</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Ngày</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Tỉnh thành</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Số 1</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Số 2</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Số 3</th>
                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1">Số đăng kí</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(isset($tips) && is_array($tips) && count($tips) > 0){
                        $count = 0;
                        foreach($tips as $item){
                            $count++;
                            ?>
                            <tr role="row <?php echo $count%2==0?'even ':'odd'?>">
                                <td><?php echo $item['pack'] ?></td>
                                <td><?php echo $item['tip_date'] ?></td>
                                <td><?php echo $province[$item['region_code']] ?></td>
                                <td><?php echo $item['num_1'] ?></td>
                                <td><?php echo $item['num_2'] ?></td>
                                <td><?php echo $item['num_3'] ?></td>
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
                    Mava_Url::buildLink('manager/list-loto-tip',$paginateParams),
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