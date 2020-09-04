<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('list_static_page'); ?></div>
        <div class="fr">
            <form name="form_search" id="form_search" action="<?php echo Mava_Url::buildLink('admin/page/index'); ?>" method="get">
                <input type="search" id="input_search_post" style="width: 200px;" class="input_text" name="q" placeholder="<?php echo __('enter_keyword_to_search') ; ?>" value="<?php echo (isset($search_tearm)?$search_tearm:'') ; ?>">
                <a href="javascript:void(0);" id="button_search_post" class="mava_button mava_button_gray" onclick="$('#form_search').submit();"><?php echo __('search') ; ?></a>
                <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/page/add'); ?>">+ <?php echo __('add_new_static_page') ; ?></a>
            </form>
        </div>
    </div>

    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('slug'); ?></th>
            <th><?php echo __('page_group'); ?></th>
            <th><?php echo __('layout'); ?></th>
            <th><?php echo __('publish_time'); ?></th>
            <th><?php echo __('unpublish_time'); ?></th>
            <th><?php echo __('created_by'); ?></th>
            <th><?php echo __('sort_order'); ?></th>
            <th><?php echo __('show_title'); ?></th>
            <th colspan="9">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($list_page) && count($list_page) > 0){
            $count = 0;
            foreach($list_page as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
                <td>'. $count .'</td>
                <td>'. htmlspecialchars($item['slug']) .'</td>
                <td>'. htmlspecialchars($item['title']) .'</td>
                <td>'. htmlspecialchars($item['layout']) .'</td>
                <td>'.($item['publish_time'] > 0 ?date('Y-m-d H:i:s', $item['publish_time']):'-') .'</td>
                <td>'.($item['unpublish_time'] > 0 ?date('Y-m-d H:i:s', $item['unpublish_time']):'-') .'</td>
                <td>'. htmlspecialchars($item['created_by']) .'</td>
                <td>'. htmlspecialchars($item['sort_order']) .'</td>
                <td>'. htmlspecialchars($item['show_title']) .'</td>
                <td width="100" align="center"><a href="'. Mava_Url::buildLink('admin/page/add_content',array('page_id' => $item['id'])) .'">'. __('add_page_content') .'</a></td>
                <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/page/edit',array('page_id' => $item['id'])) .'">'. __('edit') .'</a></td>
                <td width="50" align="center"><a onclick="return confirm(\''.__('delete_page').'\');" href="'. Mava_Url::buildLink('admin/page/delete',array('page_id' => $item['id'])) .'">'. __('delete') .'</a></td>
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
                'end' => $skip+sizeof($list_page),
                'total' => number_format($total,0,',','.')
            ));
            ?>
        </div>
    </div>
    <?php
    echo Mava_View::buildPagination(
        Mava_Url::buildLink('admin/page/index',array()),
        $total_page,
        $page,
        $page_offset,
        'phrase_list_pagination'
    );
    ?>
</div>
