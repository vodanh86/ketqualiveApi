<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('slug'); ?></div>
        <div class="fr">
            <form name="form_search" id="form_search" action="<?php echo Mava_Url::buildLink('admin/page/slug'); ?>" method="get">
                <input type="search" id="input_search_post" style="width: 200px;" class="input_text" name="q" placeholder="<?php echo __('enter_keyword_to_search') ; ?>" value="<?php echo (isset($search_tearm)?$search_tearm:'') ; ?>">
                <a href="javascript:void(0);" id="button_search_post" class="mava_button mava_button_gray" onclick="$('#form_search').submit();"><?php echo __('search') ; ?></a>
                <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/page/slug_add'); ?>">+ <?php echo __('add_slug') ; ?></a>
            </form>
        </div>
    </div>

    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('slug'); ?></th>
            <th><?php echo __('app'); ?></th>
            <th><?php echo __('controller'); ?></th>
            <th><?php echo __('action'); ?></th>
            <th><?php echo __('params'); ?></th>
            <th colspan="6">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($list_slug) && count($list_slug) > 0){
            $count = 0;
            foreach($list_slug as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
                <td>'. $count .'</td>
                <td>'. htmlspecialchars($item['slug']) .'</td>
                <td>'. htmlspecialchars($item['app']) .'</td>
                <td>'. htmlspecialchars($item['controller']) .'</td>
                <td>'. htmlspecialchars($item['action']) .'</td>
                <td>'. htmlspecialchars($item['params']) .'</td>
                <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/page/slug_edit',array('slug_id' => $item['id'])) .'">'. __('edit') .'</a></td>
                <td width="50" align="center"><a onclick="return confirm(\''.__('delete_slug').'\');" href="'. Mava_Url::buildLink('admin/page/slug_delete',array('slug_id' => $item['id'])) .'">'. __('delete') .'</a></td>
            </tr>';
            }
        }
        ?>
        </tbody>
    </table>
    <div class="list_footer clearfix fr" style="color: #5499E4; padding-top: 5px;">
        <div class="list_row_stats">
            <?php
            echo __('show_x_y_in_z_record',array(
                'start' => $skip+1,
                'end' => $skip+sizeof($list_slug),
                'total' => number_format($total,0,',','.')
            ));
            ?>
        </div>
    </div>
    <?php
    echo Mava_View::buildPagination(
        Mava_Url::buildLink('admin/page/slug',array()),
        $total_page,
        $page,
        $page_offset,
        'phrase_list_pagination'
    );
    ?>
</div>
