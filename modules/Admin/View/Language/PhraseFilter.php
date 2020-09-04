<?php
    if(isset($phrases) && sizeof($phrases) > 0){
        echo '<ul class="list_items">';
        $count = 0;
        foreach($phrases as $item){
            $count++;
            if(isset($filter_title) && $filter_title!=""){
                $phrase_title = preg_replace('/'. $filter_title .'/iu','<u>$0</u>',htmlspecialchars($item['title']));
            }else{
                $phrase_title = htmlspecialchars($item['title']);
            }
            echo '<li class="item item_'. $item['phrase_state'] .'">
                                '. ($item['canDelete']==1?'<a href="javascript:void(0);" phrase-id="'. $item['phrase_id'] .'" phrase-title="'. htmlspecialchars($item['title']) .'" class="actionButton button_delete_item button_delete_phrase"'. ($item['map_language_id']>0?' title="'. __('revert_phrase') .'">'. __('revert_phrase'):' title="'. __('delete_phrase') .'">'. __('delete_phrase')) .'</a>':'') .'
                                <h4><a class="button_edit_phrase" href="'. Mava_Url::buildLink('admin/phrase/edit',array('phraseID' => $item['phrase_id'],'languageID' => $language_id)) .'">'. $phrase_title .'</a></h4>
                                </li>';
        }
        echo '</ul>';
    }else{
        echo '<div class="no_phrase_item_found">'. __('no_phrase_found') .'</div>';
    }
?>