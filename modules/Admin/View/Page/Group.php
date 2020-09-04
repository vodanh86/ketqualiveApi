<div class="page_width">
    <div class="admin_breadcrumbs">
        <?php
        echo (isset($breadcrumbs)?Mava_View::buildBreadcrumbs($breadcrumbs):'');
        ?>
    </div>
    <div class="table_action_button clearfix">
        <div class="fl mava_table_title"><?php echo __('page_group'); ?></div>
        <div class="fr">
            <a class="mava_button mava_button_gray" href="<?php echo Mava_Url::buildLink('admin/page/group_add'); ?>">+ <?php echo __('add_group'); ?></a>
        </div>
    </div>
    <table class="mava_table">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('title'); ?></th>
            <th colspan="4">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($groups) && is_array($groups) && count($groups) > 0){
            $count = 0;
            foreach($groups as $item){
                $count++;
                echo '<tr class="'. ($count%2==0?'odd ':'') .'">
                <td>'. $count .'</td>
                <td>'. htmlspecialchars($item['title']) .'</td>
                <td width="50" align="center"><a href="'. Mava_Url::buildLink('admin/page/group_edit',array('groupID' => $item['id'])) .'">'. __('edit') .'</a></td>
                <td width="50" align="center"><a onclick="return confirm(\''.__('delete_page_group').'\');" href="'. Mava_Url::buildLink('admin/page/group_delete',array('groupID' => $item['id'])) .'">'. __('delete') .'</a></td>
            </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>
