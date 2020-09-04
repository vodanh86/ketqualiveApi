<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 4/25/14
 * Time: 2:06 PM
 * To change this template use File | Settings | File Templates.
 */
class Admin_Controller_Option extends Mava_AdminController {
    public function indexAction(){
        $page = max((int)Mava_Url::getParam('page'),1);
        $limit = max(Mava_Application::get('options')->defaultAdminROP,50);
        $skip = ($page-1)*$limit;
        $pageOffset = max(Mava_Application::get('options')->defaultAdminPaginationOffset,5);
        $optionModel = $this->_getOptionModel();
        $optionGroup = $optionModel->getAllOptionGroup();

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('admin_option')
        );

        // check max page
        $maxPage = max(ceil(sizeof($optionGroup)/$limit),1);
        if($page > $maxPage){
            return $this->responseRedirect(Mava_Url::buildLink('admin/option/index',array(
                'page' => $maxPage
            )));
        }

        // seo
        Mava_Application::set('seo/title',__('admin_option'));

        return $this->responseView('Admin_View_Option_Group',array(
            'breadcrumbs' => $breadcrumbs,
            'option_group' => $optionGroup,
            'total' => sizeof($optionGroup),
            'total_page' => $maxPage,
            'skip' => $skip,
            'limit' => $limit,
            'page_offset' => $pageOffset,
            'page' => $page
        ));
    }

    public function edit_groupAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $addonModel = $this->_getAddonModel();
        $addon = $addonModel->getAllAddon();
        $groupId = Mava_Url::getParam('groupID');
        $optionModel = $this->_getOptionModel();
        $optionGroup = $optionModel->getOptionGroupById($groupId);
        if($optionGroup){
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/index/index'),
                'text' => __('admin_page')
            );
            $breadcrumbs[] = array(
                'url' => Mava_Url::buildLink('admin/option/index'),
                'text' => __('admin_option')
            );
            $breadcrumbs[] = array(
                'url' => '',
                'text' => __('edit_option_group')
            );

            // seo
            Mava_Application::set('seo/title',__('add_option_group'));

            $languageModel = $this->_getLanguageModel();
            $groupTitle = $languageModel->getPhraseByTitle('_option_group_title_'. $groupId,0);
            if($groupTitle){
                $groupTitle = $groupTitle['phrase_text'];
            }else{
                $groupTitle = '';
            }

            $groupDescription = $languageModel->getPhraseByTitle('_option_group_description_'. $groupId,0);
            if($groupDescription){
                $groupDescription = $groupDescription['phrase_text'];
            }else{
                $groupDescription = '';
            }

            return $this->responseView('Admin_View_Option_EditGroup',array(
                'breadcrumbs' => $breadcrumbs,
                'optionGroup' => $optionGroup,
                'groupID' => $groupId,
                'groupTitle' => $groupTitle,
                'groupDescription' => $groupDescription,
                'addon' => $addon
            ));
        }else{
            return $this->responseError(__('option_group_not_found'), Mava_Error::NOT_FOUND);
        }
    }

    public function do_edit_groupAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $currentGroupID = Mava_Url::getParam('currentGroupID');
        $groupID = Mava_Url::getParam('groupID');
        $groupTitle = Mava_Url::getParam('groupTitle');
        $groupDescription = Mava_Url::getParam('groupDescription');
        $groupDisplayOrder = Mava_Url::getParam('groupDisplayOrder');
        $groupDebugOnly = (int)Mava_Url::getParam('groupDebugOnly');
        $addOnID = Mava_Url::getParam('addOnID');
        $optionModel = $this->_getOptionModel();
        $data = array();
        $optionGroup = $optionModel->getOptionGroupById($currentGroupID);
        if(!$optionGroup){
            $data = array(
                'status' => -1,
                'message' => __('option_group_not_found')
            );
        }else if($groupID==""){
            $data = array(
                'status' => -1,
                'message' => __('option_group_id_empty')
            );
        }else if($optionModel->isOptionGroupExist($groupID,array($currentGroupID))){
            $data = array(
                'status' => -1,
                'message' => __('option_group_id_existed')
            );
        }else if($groupTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('option_group_title_empty')
            );
        }else{
            $check = $optionModel->editOptionGroup($currentGroupID, array(
                'group_id' => $groupID,
                'display_order' => $groupDisplayOrder,
                'debug_only' => $groupDebugOnly,
                'addon_id' => $addOnID
            ));
            if($check){
                // add title, description
                $languageModel = $this->_getLanguageModel();
                $titlePhraseKey = '_option_group_title_'. $groupID;
                $titleExist = $languageModel->isPhraseExist($titlePhraseKey,0);
                if($titleExist){
                    $titlePhrase = $languageModel->getPhraseByTitle($titlePhraseKey,0);
                    $languageModel->editPhrase($titlePhrase,0,$titlePhraseKey,$groupTitle,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$titlePhraseKey,$groupTitle, $addOnID);
                }

                $descriptionPhraseKey = '_option_group_description_'. $groupID;
                $descriptionExist = $languageModel->isPhraseExist($descriptionPhraseKey,0);
                if($descriptionExist){
                    $descriptionPhrase = $languageModel->getPhraseByTitle($descriptionPhraseKey,0);
                    $languageModel->editPhrase($descriptionPhrase,0,$descriptionPhraseKey,$groupDescription,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$descriptionPhraseKey,$groupDescription, $addOnID);
                }

                $data = array(
                    'status' => 1,
                    'message' => __('option_group_edit_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_group_edit_failed')
                );
            }
        }

        return $this->responseJson($data);
    }

    public function add_groupAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $addonModel = $this->_getAddonModel();
        $addon = $addonModel->getAllAddon();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/option/index'),
            'text' => __('admin_option')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('add_option_group')
        );

        // seo
        Mava_Application::set('seo/title',__('add_option_group'));

        return $this->responseView('Admin_View_Option_AddGroup',array(
            'breadcrumbs' => $breadcrumbs,
            'addon' => $addon
        ));
    }

    public function do_add_groupAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $groupID = Mava_Url::getParam('groupID');
        $groupTitle = Mava_Url::getParam('groupTitle');
        $groupDescription = Mava_Url::getParam('groupDescription');
        $groupDisplayOrder = Mava_Url::getParam('groupDisplayOrder');
        $groupDebugOnly = (int)Mava_Url::getParam('groupDebugOnly');
        $addOnID = Mava_Url::getParam('addOnID');
        $optionModel = $this->_getOptionModel();
        $data = array();
        if($groupID==""){
            $data = array(
                'status' => -1,
                'message' => __('option_group_id_empty')
            );
        }else if($optionModel->isOptionGroupExist($groupID)){
            $data = array(
                'status' => -1,
                'message' => __('option_group_id_existed')
            );
        }else if($groupTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('option_group_title_empty')
            );
        }else{
            $check = $optionModel->addOptionGroup($groupID, $groupDisplayOrder, $groupDebugOnly, $addOnID);
            if($check){
                // add title, description
                $languageModel = $this->_getLanguageModel();
                $titlePhraseKey = '_option_group_title_'. $groupID;
                $titleExist = $languageModel->isPhraseExist($titlePhraseKey,0);
                if($titleExist){
                    $titlePhrase = $languageModel->getPhraseByTitle($titlePhraseKey,0);
                    $languageModel->editPhrase($titlePhrase,0,$titlePhraseKey,$groupTitle,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$titlePhraseKey,$groupTitle, $addOnID);
                }

                $descriptionPhraseKey = '_option_group_description_'. $groupID;
                $descriptionExist = $languageModel->isPhraseExist($descriptionPhraseKey,0);
                if($descriptionExist){
                    $descriptionPhrase = $languageModel->getPhraseByTitle($descriptionPhraseKey,0);
                    $languageModel->editPhrase($descriptionPhrase,0,$descriptionPhraseKey,$groupDescription,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$descriptionPhraseKey,$groupDescription, $addOnID);
                }

                $data = array(
                    'status' => 1,
                    'message' => __('option_group_add_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_group_add_failed')
                );
            }
        }

        return $this->responseJson($data);
    }

    public function delete_groupAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $groupID = Mava_Url::getParam('groupID');
        $optionModel = $this->_getOptionModel();
        $data = array();
        if($groupID!=""){
            $group = $optionModel->getOptionGroupById($groupID);
            if($group){
                $check = $optionModel->deleteOptionGroup($groupID);
                if($check){
                    $data = array(
                        'status' => 1,
                        'message' => __('option_group_delete_success')
                    );
                }else{
                    $data = array(
                        'status' => -1,
                        'message' => __('option_group_delete_failed')
                    );
                }
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_group_not_found')
                );
            }
        }else{
            $data = array(
                'status' => -1,
                'message' => __('option_group_not_found')
            );
        }
        return $this->responseJson($data);
    }

    public function edit_group_sortAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $optionModel = $this->_getOptionModel();
        $optionGroup = $optionModel->getAllOptionGroup();
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/index/index'),
            'text' => __('admin_page')
        );
        $breadcrumbs[] = array(
            'url' => Mava_Url::buildLink('admin/option/index'),
            'text' => __('admin_option')
        );
        $breadcrumbs[] = array(
            'url' => '',
            'text' => __('display_order')
        );

        // seo
        Mava_Application::set('seo/title',__('edit_option_group_sort'));
        return $this->responseView('Admin_View_Option_EditGroupSort',array(
            'breadcrumbs' => $breadcrumbs,
            'option_group' => $optionGroup
        ));
    }

    public function do_edit_group_sortAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $displayOrder = Mava_Url::getParam('groupDisplayOrder');
        $optionModel = $this->_getOptionModel();
        if(isset($displayOrder) && sizeof($displayOrder) > 0){
            $optionModel->editOptionGroupSort($displayOrder);
        }
        return $this->responseJson(array(
            'status' => 1,
            'message' => __('edit_option_group_order_success')
        ));
    }

    public function settingAction(){
        $groupID = Mava_Url::getParam('groupID');
        if($groupID!=""){
            $optionModel = $this->_getOptionModel();
            $optionGroup = $optionModel->getOptionGroupById($groupID);
            if($optionGroup){
                if($optionGroup['debug_only'] == 1 && !is_debug()){
                    return $this->responseError(__('run_on_debug_mode'), 403);
                }
                $options = $optionModel->getOptionByGroupId($groupID);
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/index'),
                    'text' => __('admin_option')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('_option_group_title_'. $optionGroup['group_id'])
                );

                // seo
                Mava_Application::set('seo/title',__('options') .": ". __('_option_group_title_'. $optionGroup['group_id']));
                return $this->responseView('Admin_View_Option_Setting',array(
                    'optionGroup' => $optionGroup,
                    'breadcrumbs' => $breadcrumbs,
                    'options' => $options
                ));
            }else{
                return $this->responseError(__('option_group_not_found'),Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseRedirect(Mava_Url::buildLink('admin/option/index'));
        }
    }

    public function add_optionAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $groupID = Mava_Url::getParam('groupID');
        if($groupID!=""){
            $optionModel = $this->_getOptionModel();
            $optionGroup = $optionModel->getOptionGroupById($groupID);
            if($optionGroup){
                $options = $optionModel->getOptionByGroupId($groupID);
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/index'),
                    'text' => __('admin_option')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/setting',array('groupID' => $optionGroup['group_id'])),
                    'text' => __('_option_group_title_'. $optionGroup['group_id'])
                );

                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('add_option')
                );

                // seo
                Mava_Application::set('seo/title',__('add_option') .": ". __('_option_group_title_'. $optionGroup['group_id']));
                $addonModel = $this->_getAddonModel();
                $addon = $addonModel->getAllAddon();
                $allOptionGroup = $optionModel->getAllOptionGroup();
                return $this->responseView('Admin_View_Option_Add',array(
                    'optionGroup' => $optionGroup,
                    'breadcrumbs' => $breadcrumbs,
                    'addon' => $addon,
                    'option_group' => $allOptionGroup,
                    'options' => $options
                ));
            }else{
                return $this->responseError(__('option_group_not_found'),Mava_Error::NOT_FOUND);
            }
        }else{
            return $this->responseRedirect(Mava_Url::buildLink('admin/option/index'));
        }
    }

    public function do_add_optionAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $optionID = Mava_Url::getParam('optionID');
        $optionTitle = Mava_Url::getParam('optionTitle');
        $optionDescription = Mava_Url::getParam('optionDescription');
        $optionEditFormat = Mava_Url::getParam('optionEditFormat');
        $optionFormatParameters = Mava_Url::getParam('optionFormatParameters');
        $optionDataType = Mava_Url::getParam('optionDataType');
        $optionDefaultValue = Mava_Url::getParam('optionDefaultValue');
        $optionSubOption = Mava_Url::getParam('optionSubOption');
        $optionValidationCallbackClass = Mava_Url::getParam('optionValidationCallbackClass');
        $optionValidationCallbackMethod = Mava_Url::getParam('optionValidationCallbackMethod');
        $optionDisplayInGroup = Mava_Url::getParam('optionDisplayInGroup');
        $optionGroupDisplayOrder = Mava_Url::getParam('optionGroupDisplayOrder');
        $addOnID = Mava_Url::getParam('addOnID');
        $data = array();
        $optionModel = $this->_getOptionModel();
        if($optionID==""){
            $data = array(
                'status' => -1,
                'message' => __('option_id_empty')
            );
        }else if($optionTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('option_title_empty')
            );
        }else if($optionEditFormat=="" ||
            !in_array($optionEditFormat,array(
                'textbox',
                'spinbox',
                'onoff',
                'onofftextbox',
                'radio',
                'select',
                'checkbox',
                'template',
                'callback'
            ))
        ){
            $data = array(
                'status' => -1,
                'message' => __('option_edit_format_invalid')
            );
        }else if($optionDataType=="" ||
            !in_array($optionDataType,array(
                'boolean',
                'string',
                'integer',
                'array'
            ))
        ){
            $data = array(
                'status' => -1,
                'message' => __('option_data_type_invalid')
            );
        }else if(!is_array($optionDisplayInGroup) || sizeof($optionDisplayInGroup)==0){
            $data = array(
                'status' => -1,
                'message' => __('select_group_where_option_display')
            );
        }else if($optionModel->isOptionExist($optionID)){
            $data = array(
                'status' => -1,
                'message' => __('option_existed')
            );
        }else if($optionValidationCallbackClass!="" && !class_exists($optionValidationCallbackClass)){
            $data = array(
                'status' => -1,
                'message' => __('callback_class_not_found')
            );
        }else if($optionValidationCallbackClass!="" && !method_exists(new $optionValidationCallbackClass(),$optionValidationCallbackMethod)){
            $data = array(
                'status' => -1,
                'message' => __('callback_method_not_found')
            );
        }else{
            $check = $optionModel->addOption(
                $optionID,
                $optionEditFormat,
                $optionDataType,
                $addOnID,
                $optionFormatParameters,
                $optionDefaultValue,
                $optionSubOption,
                $optionValidationCallbackClass,
                $optionValidationCallbackMethod,
                $optionDisplayInGroup,
                $optionGroupDisplayOrder
            );
            if($check){
                // add title, description
                $languageModel = $this->_getLanguageModel();
                $titlePhraseKey = '_option_title_'. $optionID;
                $titleExist = $languageModel->isPhraseExist($titlePhraseKey,0);
                if($titleExist){
                    $titlePhrase = $languageModel->getPhraseByTitle($titlePhraseKey,0);
                    $languageModel->editPhrase($titlePhrase,0,$titlePhraseKey,$optionTitle,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$titlePhraseKey,$optionTitle, $addOnID);
                }

                $descriptionPhraseKey = '_option_description_'. $optionID;
                $descriptionExist = $languageModel->isPhraseExist($descriptionPhraseKey,0);
                if($descriptionExist){
                    $descriptionPhrase = $languageModel->getPhraseByTitle($descriptionPhraseKey,0);
                    $languageModel->editPhrase($descriptionPhrase,0,$descriptionPhraseKey,$optionDescription,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$descriptionPhraseKey,$optionDescription, $addOnID);
                }
                $data = array(
                    'status' => 1,
                    'message' => __('option_add_success')
                );
            }else{
                $data = array(
                    'status' => 1,
                    'message' => __('option_add_failed')
                );
            }
        }

        return $this->responseJson($data);
    }

    public function save_settingAction(){
        $groupID = Mava_Url::getParam("groupID");
        $option = Mava_Url::getParam("option");
        $data = array();
        if($groupID!=""){
            $optionModel = $this->_getOptionModel();
            $optionGroup = $optionModel->getOptionGroupById($groupID);
            if($optionGroup) {
                if ($optionGroup['debug_only'] == 1 && !is_debug()) {
                    $data = array(
                        'status' => -1,
                        'message' => __('run_on_debug_mode')
                    );
                }else{
                    $options = $optionModel->getOptionByGroupId($groupID);
                    if (sizeof($options) > 0) {
                        foreach ($options as $item) {
                            if (isset($option[$item['option_id']])) {
                                if ($item['data_type'] == 'integer' || $item['data_type'] == 'boolean') {
                                    $optionModel->editOption($item['option_id'], array(
                                        'option_value' => (int)$option[$item['option_id']]
                                    ));
                                } else if ($item['data_type'] == 'string') {
                                    if (is_array($option[$item['option_id']])) {
                                        $option[$item['option_id']] = json_encode($option[$item['option_id']]);
                                    }
                                    $optionModel->editOption($item['option_id'], array(
                                        'option_value' => $option[$item['option_id']]
                                    ));
                                } else if ($item['data_type'] == 'array') {
                                    if (is_array($option[$item['option_id']])) {
                                        $option[$item['option_id']] = json_encode($option[$item['option_id']]);
                                    } else {
                                        $option[$item['option_id']] = json_encode(array());
                                    }
                                    $optionModel->editOption($item['option_id'], array(
                                        'option_value' => $option[$item['option_id']]
                                    ));
                                }
                            } else {
                                $optionModel->editOption($item['option_id'], array(
                                    'option_value' => ''
                                ));
                            }
                        }
                    }
                    $data = array(
                        'status' => 1,
                        'message' => __('option_setting_saved')
                    );
                }
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_group_not_found')
                );
            }
        }else{
            $data = array(
                'status' => -1,
                'message' => __('option_group_not_found')
            );
        }

        return $this->responseJson($data);
    }

    public function upload_imageAction(){
        $optionID = Mava_Url::getParam('optionID');
        $data = array();
        if($optionID==''){
            $data = array(
                'status' => -1,
                'message' => __('option_not_found')
            );
        }else if(!isset($_FILES['image_uploader_input_'. $optionID])){
            $data = array(
                'status' => -1,
                'message' => __('please_select_file_to_upload')
            );
        }else{
            $optionModel = $this->_getOptionModel();
            $option = $optionModel->getOptionById($optionID);
            if($option){
                $fileType = $_FILES['image_uploader_input_'. $optionID]['type'];
                if($fileType=='image/png'){
                    $fileExt = 'png';
                }else if($fileType=='image/gif'){
                    $fileExt = 'gif';
                }else if($fileType=='image/x-icon'){
                    $fileExt = 'ico';
                }else if($fileType=='image/bmp'){
                    $fileExt = 'bmp';
                }else if($fileType=='image/jpg' || $fileType=='image/jpeg'){
                    $fileExt = 'jpg';
                }else{
                    $fileExt = '';
                }
                if($fileExt==''){
                    $data = array(
                        'status' => -1,
                        'message' => __('upload_file_type_invalid')
                    );
                }else{
                    $fileName = md5($optionID . time()) .'.'. $fileExt;
                    $uploadImageFolder = Mava_Application::get('config/uploadImage/folder');
                    $uploadDir = BASEDIR .'/'. $uploadImageFolder .'/option';
                    $uploadPath = $uploadDir .'/'. $fileName;
                    if(!is_dir($uploadDir) && mkdir($uploadDir, 0777)){
                        $data = array(
                            'status' => -1,
                            'message' => __('upload_image_dir_not_exist')
                        );
                    }else{
                        if(@move_uploaded_file($_FILES['image_uploader_input_'. $optionID]['tmp_name'],$uploadPath)){
                            $data = array(
                                'status' => 1,
                                'message' => __('image_uploaded'),
                                'optionID' => $optionID,
                                'fileName' => $fileName,
                                'fileUrl' => get_static_domain() .'/'. $uploadImageFolder .'/option/'. $fileName
                            );
                        }else{
                            $data = array(
                                'status' => -1,
                                'message' => __('can_not_upload_image')
                            );
                        }
                    }
                }
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_not_found')
                );
            }
        }

        return $this->responseView('Mava_View_Option_uploadSingleImageFinish',$data,'blank');
    }

    public function edit_option_sortAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $groupID = Mava_Url::getParam('groupID');
        if($groupID==''){
            return $this->responseError(__('invalid_request'),Mava_Error::INVALID_REQUEST);
        }else{
            $optionModel = $this->_getOptionModel();
            $group = $optionModel->getOptionGroupById($groupID);
            if($group){
                $options = $optionModel->getOptionByGroupId($groupID);
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/index'),
                    'text' => __('admin_option')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/setting',array('groupID' => $groupID)),
                    'text' => __('_option_group_title_'. $groupID)
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('display_order')
                );

                // seo
                Mava_Application::set('seo/title',__('edit_option_sort'));
                return $this->responseView('Admin_View_Option_EditSort',array(
                    'groupID' => $groupID,
                    'options' => $options,
                    'breadcrumbs' => $breadcrumbs
                ));
            }else{
                return $this->responseError(__('option_group_not_found'),Mava_Error::NOT_FOUND);
            }
        }
    }

    public function do_edit_option_sortAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $groupID = Mava_Url::getParam('groupID');
        $displayOrder = Mava_Url::getParam('optionDisplayOrder');
        $optionModel = $this->_getOptionModel();
        if(isset($displayOrder) && sizeof($displayOrder) > 0 && $groupID!=""){
            $optionModel->editOptionSort($displayOrder, $groupID);
        }
        return $this->responseJson(array(
            'status' => 1,
            'message' => __('edit_option_order_success')
        ));
    }

    public function edit_optionAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $optionID = Mava_Url::getParam('optionID');
        if($optionID==''){
            return $this->responseError(__('invalid_request'),Mava_Error::INVALID_REQUEST);
        }else{
            $optionModel = $this->_getOptionModel();
            $option = $optionModel->getOptionById($optionID);
            $sortOption = $optionModel->getOptionDisplayData($optionID);
            $sortData = array();
            $sortKey = array();
            if($sortOption && sizeof($sortOption) > 0){
                foreach($sortOption as $item){
                    $sortKey[] = $item['group_id'];
                    $sortData[$item['group_id']] = $item['display_order'];
                }
            }

            $addonModel = $this->_getAddonModel();
            $addon = $addonModel->getAllAddon();
            if($option){
                $breadcrumbs = array();
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/index/index'),
                    'text' => __('admin_page')
                );
                $breadcrumbs[] = array(
                    'url' => Mava_Url::buildLink('admin/option/index'),
                    'text' => __('admin_option')
                );
                $breadcrumbs[] = array(
                    'url' => '',
                    'text' => __('edit_option')
                );

                // seo
                Mava_Application::set('seo/title',__('edit_option'));

                $languageModel = $this->_getLanguageModel();
                $optionTitle = $languageModel->getPhraseByTitle('_option_title_'. $optionID,0);
                if($optionTitle){
                    $optionTitle = $optionTitle['phrase_text'];
                }else{
                    $optionTitle = '';
                }

                $optionDescription = $languageModel->getPhraseByTitle('_option_description_'. $optionID,0);
                if($optionDescription){
                    $optionDescription = $optionDescription['phrase_text'];
                }else{
                    $optionDescription = '';
                }

                $optionGroup = $optionModel->getAllOptionGroup();
                return $this->responseView('Admin_View_Option_Edit',array(
                    'breadcrumbs' => $breadcrumbs,
                    'option' => $option,
                    'addon' => $addon,
                    'optionTitle' => $optionTitle,
                    'optionDescription' => $optionDescription,
                    'option_group' => $optionGroup,
                    'sortData' => $sortData,
                    'sortKey' => $sortKey
                ));
            }else{
                return $this->responseError(__('option_not_found'),Mava_Error::NOT_FOUND);
            }
        }
    }

    public function do_edit_optionAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $currentOptionID = Mava_Url::getParam("currentOptionID");
        $optionID = Mava_Url::getParam("optionID");
        $addOnID = Mava_Url::getParam("addOnID");
        $optionTitle = Mava_Url::getParam("optionTitle");
        $optionDescription = Mava_Url::getParam("optionDescription");
        $optionEditFormat = Mava_Url::getParam("optionEditFormat");
        $optionFormatParameters = Mava_Url::getParam("optionFormatParameters");
        $optionDataType = Mava_Url::getParam("optionDataType");
        $optionDefaultValue = Mava_Url::getParam("optionDefaultValue");
        $optionSubOption = Mava_Url::getParam("optionSubOption");
        $optionValidationCallbackClass = Mava_Url::getParam("optionValidationCallbackClass");
        $optionValidationCallbackMethod = Mava_Url::getParam("optionValidationCallbackMethod");
        $optionDisplayInGroup = Mava_Url::getParam("optionDisplayInGroup");
        $optionGroupDisplayOrder = Mava_Url::getParam("optionGroupDisplayOrder");
        $data = array();
        $optionModel = $this->_getOptionModel();
        $option = $optionModel->getOptionById($currentOptionID);
        if(!$option){
            $data = array(
                'status' => -1,
                'message' => __('option_not_found')
            );
        }else if($currentOptionID==""){
            $data = array(
                'status' => -1,
                'message' => __('invalid_request')
            );
        }else if($optionID==""){
            $data = array(
                'status' => -1,
                'message' => __('option_id_empty')
            );
        }else if($optionTitle==""){
            $data = array(
                'status' => -1,
                'message' => __('option_title_empty')
            );
        }else if($optionEditFormat=="" ||
            !in_array($optionEditFormat,array(
                'textbox',
                'spinbox',
                'onoff',
                'onofftextbox',
                'radio',
                'select',
                'checkbox',
                'template',
                'callback'
            ))
        ){
            $data = array(
                'status' => -1,
                'message' => __('option_edit_format_invalid')
            );
        }else if($optionDataType=="" ||
            !in_array($optionDataType,array(
                'boolean',
                'string',
                'integer',
                'array'
            ))
        ){
            $data = array(
                'status' => -1,
                'message' => __('option_data_type_invalid')
            );
        }else if(!is_array($optionDisplayInGroup) || sizeof($optionDisplayInGroup)==0){
            $data = array(
                'status' => -1,
                'message' => __('select_group_where_option_display')
            );
        }else if($optionModel->isOptionExist($optionID,array($currentOptionID))){
            $data = array(
                'status' => -1,
                'message' => __('option_existed')
            );
        }else if($optionValidationCallbackClass!="" && !class_exists($optionValidationCallbackClass)){
            $data = array(
                'status' => -1,
                'message' => __('callback_class_not_found')
            );
        }else if($optionValidationCallbackClass!="" && !method_exists(new $optionValidationCallbackClass(),$optionValidationCallbackMethod)){
            $data = array(
                'status' => -1,
                'message' => __('callback_method_not_found')
            );
        }else{
            $updateData = array(
                'option_id' => $optionID,
                'default_value' => $optionDefaultValue,
                'edit_format' => $optionEditFormat,
                'edit_format_params' => $optionFormatParameters,
                'data_type' => $optionDataType,
                'sub_options' => $optionSubOption,
                'validation_class' => $optionValidationCallbackClass,
                'validation_method' => $optionValidationCallbackMethod,
                'addon_id' => $addOnID
            );
            if($updateData['data_type'] != $option['data_type']){
                $updateData['option_value'] = '';
            }
            $check = $optionModel->editOption($currentOptionID, $updateData, $optionDisplayInGroup, $optionGroupDisplayOrder);

            if($check){
                // add title, description
                $languageModel = $this->_getLanguageModel();
                $titlePhraseKey = '_option_title_'. $optionID;
                $titleExist = $languageModel->isPhraseExist($titlePhraseKey,0);
                if($titleExist){
                    $titlePhrase = $languageModel->getPhraseByTitle($titlePhraseKey,0);
                    $languageModel->editPhrase($titlePhrase,0,$titlePhraseKey,$optionTitle,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$titlePhraseKey,$optionTitle, $addOnID);
                }

                $descriptionPhraseKey = '_option_description_'. $optionID;
                $descriptionExist = $languageModel->isPhraseExist($descriptionPhraseKey,0);
                if($descriptionExist){
                    $descriptionPhrase = $languageModel->getPhraseByTitle($descriptionPhraseKey,0);
                    $languageModel->editPhrase($descriptionPhrase,0,$descriptionPhraseKey,$optionDescription,$addOnID);
                }else{
                    $languageModel->addPhrase(0,$descriptionPhraseKey,$optionDescription, $addOnID);
                }
                $data = array(
                    'status' => 1,
                    'message' => __('option_edit_success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_edit_failed')
                );
            }
        }

        return $this->responseJson($data);
    }

    public function deleteAction(){
        if(!is_debug()){
            return $this->responseError(__('run_on_debug_mode'), 403);
        }
        $optionID = Mava_Url::getParam("optionID");
        $data = array();
        if($optionID!=""){
            $optionModel = $this->_getOptionModel();
            $option = $optionModel->getOptionById($optionID);
            if($option){
                $optionModel->deleteOption($optionID);
                $data = array(
                    'status' => 1,
                    'message' => __('success')
                );
            }else{
                $data = array(
                    'status' => -1,
                    'message' => __('option_not_found',Mava_Error::NOT_FOUND)
                );
            }
        }else{
            $data = array(
                'status' => -1,
                'message' => __('invalid_request',Mava_Error::INVALID_REQUEST)
            );
        }

        return $this->responseJson($data);
    }


    /**
     * @return Mava_Model_Language
     */
    protected function _getLanguageModel(){
        return $this->getModelFromCache('Mava_Model_Language');
    }

    /**
     * @return Mava_Model_Option
     */
    protected function _getOptionModel(){
        return $this->getModelFromCache('Mava_Model_Option');
    }

    /**
     * @return Mava_Model_Addon
     */
    protected function _getAddonModel(){
        return $this->getModelFromCache('Mava_Model_Addon');
    }
}