<div class="pg-title charge-coin-title"><?php echo __('Danh sách nạp coin') .' ('.$total.')' . ($searchTerm!=""?": ". htmlspecialchars($searchTerm):""); ?></div>
<div class="pg-content charge-coin-content">
   <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
      <div class="row mb-4">
         <div class="col-md-6">
            <form name="form_search_item" id="form_search_item" action="<?php echo Mava_Url::buildLink('manager/charge-coin'); ?>" method="get">
               <div class="input-group">
                  <input type="text" class="form-control" name="q" placeholder="<?php echo __('enter_keyword_to_search'); ?>" value="<?php echo isset($searchTerm)?htmlspecialchars($searchTerm):""; ?>">
                  <span class="input-group-btn input-group-append">
                     <button class="btn btn-outline-secondary" type="submit"></i><?= __('Tìm kiếm')?></button>
                  </span>
               </div>
            </form>
         </div>
      </div>
      <div class="row mb-4">
         <div class="col-md-12 col-sm-12 col-12">
            <table id="datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
               <thead>
                  <tr role="row">
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" style="width: 157px;">User ID</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 259px;">Tên</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 117px;">Email</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 60px;">Điện thoại</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 115px;">Coin nạp</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">Coin trước khi nạp</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">Coin sau khi nạp</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 115px;">Kênh nạp</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 115px;">Data</th>
                      <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 115px;">Ngày nạp</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  if(isset($logs) && is_array($logs) && count($logs) > 0){
                     $count = 0;
                     foreach($logs as $item){
                        $count++;
                        echo '
                        <tr role="row" class="'. ($count%2==0?'even ':'odd') .'">
                           <td class="sorting_1">'.$item['user_id'].'</td>
                           <td>'.$item['custom_title'].'</td>
                           <td>'.$item['email'].'</td>
                           <td>'.$item['phone'].'</td>
                           <td>'.number_format($item['coin_change'], 0, ',', '.').'</td>
                           <td>'.number_format($item['coin_before'], 0, ',', '.').'</td>
                           <td>'.number_format($item['coin_after'], 0, ',', '.').'</td>
                           <td>'.$item['type'].'</td>
                           <td>'.$item['data'].'</td>
                           <td>'.($item['created_at'] > 0 ? date('H:i, d-m-Y', $item['created_at']) : '').'</td>
                        </tr>
                        ';
                     }
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
               if($searchTerm != ""){
                  $paginateParams['q'] = $searchTerm;
               }
               echo Mava_View::buildPaginationBootrap(
                  Mava_Url::buildLink('manager/charge-coin',$paginateParams),
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