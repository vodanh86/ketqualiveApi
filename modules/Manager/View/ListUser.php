<div class="pg-title user-vip-title"><?php echo __('Danh sách thành viên') .' ('.$total.')' . ($searchTerm!=""?": ". htmlspecialchars($searchTerm):""); ?></div>
<div class="pg-content user-vip-content">
   <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
      <div class="row">
         <div class="col-md-6 col-sm-6 col-12 mb-2">
            <form name="form_search_item" id="form_search_item" action="<?php echo Mava_Url::buildLink('manager/user'); ?>" method="get">
               <div class="input-group">
                  <input type="text" class="form-control" name="q" placeholder="<?php echo __('enter_keyword_to_search'); ?>" value="<?php echo isset($searchTerm)?htmlspecialchars($searchTerm):""; ?>">
                  <span class="input-group-btn input-group-append">
                     <button class="btn btn-outline-secondary" type="submit"></i><?= __('Tìm kiếm')?></button>
                  </span>
               </div>
            </form>
         </div>
          <div class="col-md-6 col-sm-6 col-12 mb-2 text-right">
              <a href="user-vip"><button type="button" class="btn btn-secondary ml-2 mb-2">Vip</button></a>
              <a href="user-supervip"><button type="button" class="btn btn-secondary ml-2 mb-2">Supervip</button></a>
          </div>
      </div>
      <div class="row mb-4">
         <div class="col-md-12 col-sm-12 col-12">
            <table id="datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
               <thead>
                  <tr role="row">
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">ID</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 259px;">Tên</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 117px;">Coin</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 60px;">Email</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 115px;">Điện thoại</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">Vip</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">Supervip</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"style="width: 90px;">Khóa</th>
                     <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1"></th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  if(isset($users) && is_array($users) && count($users) > 0){
                     $count = 0;
                     foreach($users as $item){
                        $count++;
                        echo '
                        <tr role="row" class="'. ($count%2==0?'even ':'odd') .'">
                            <td>'.$item['user_id'].'</td>
                            <td>'.$item['custom_title'].'</td>
                            <td>'.number_format($item['coin'], 0, ',', '.').'</td>
                            <td>'.$item['email'].'</td>
                            <td>'.$item['phone'].'</td>
                            <td>'.($item['expired_vip'] > 0 ? date('H:i, d-m-Y', $item['expired_vip']) : '').'</td>
                            <td>'.($item['is_supervip'] == 1 ? "Có" : "Không").'</td>
                            <td>'.($item['lock_account'] > 0 ? date('H:i, d-m-Y', $item['lock_account']) : '').'</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="'."increase-coin?user_id=".$item['user_id'].'">Cộng coin</a>
                                        <a class="dropdown-item" href="'."upgrade-vip?user_id=".$item['user_id'].'">Nâng Vip</a>';
                                         if($item['is_supervip'] != 1) {
                                             echo '<a class="dropdown-item" href="' . "upgrade-supervip?user_id=" . $item['user_id'] . '">Nâng Supervip</a>';
                                         }
                                    echo '
                                        <a class="dropdown-item" href="'."reset-password?user_id=".$item['user_id'].'">Đặt lại mật khẩu</a>
                                        <a class="dropdown-item" href="'."lock-account?user_id=".$item['user_id'].'">Khóa tài khoản</a>
                                    </div>
                                </div>
                            </td>
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
                  Mava_Url::buildLink('manager/user',$paginateParams),
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