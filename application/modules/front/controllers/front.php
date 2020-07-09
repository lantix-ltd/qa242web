<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Front extends MX_Controller {
	protected $data = '';
		function __construct() {
		parent::__construct();
		$this->load->library("pagination");
		 $this->load->helper("url");
		 $this->load->library('session');
		}

		function index() {
		    $supplier_id=$this->session->userdata['supplier']['supplier_id'];
		    if (empty($supplier_id)) {
    			redirect(BASE_URL.'login');
    			exit();
			}
			$data['ingredients'] = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET.'_ingredients_supplier',"","","ingredient_id,s_item_name","")->result_array();
		    $data['detail'] = Modules::run('ingredients/_get_data_from_db_table',array("id"=>$supplier_id),"supplier","","","*","")->row_array();
			$data['supplier_types'] = Modules::run('ingredients/_get_data_from_db_table',array("status"=>"1"),'supplier_type',"","","id,name","")->result_array();
			$data['type'] = Modules::run('api/_get_specific_table_with_pagination_where_groupby',array("status" =>'1'),'id desc','id','ingredient_types','id,name','1','0','','','')->result_array();
			$ing_doc=$temp=array();
			$ingredients = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET.'_ingredients_supplier',"","","ingredient_id,s_item_name","")->result_array();
			foreach($ingredients as $ingrdnt => $ing)
			{
				$type_selected=$this->supplier_ingredients($supplier_id,$ing['ingredient_id'])->result_array();
				foreach($type_selected as $key => $value)
				{
					$result = Modules::run('ingredients/_get_data_from_db_table',array("type_id"=>$value['type_id'],"assign_to"=>"ingredient","status"=>"1"),'document',"","","doc_name,id","")->result_array();
					foreach($result as $keys => $values)
					{
						$query=Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id,"ingredient_id"=>$ing['ingredient_id'],"document_id"=>$values['id']),DEFAULT_OUTLET.'_ingredients_document',"","","document","")->row_array();
						$ing_doc['document']=null;
						if(!empty($query) && isset($query['document']))
						$ing_doc['document']=$query['document'];
						$ing_doc['doc_name']=$values['doc_name'];
						$ing_doc['ing_id']=$ing['ingredient_id'];
						$ing_doc['ing_name']=$ing['s_item_name'];
						$temp[]=$ing_doc;
					}
				}
			}
			$data['ingredients_doc']=$temp;
			$this->load->module('template');
		    $data['header_file'] = 'header';
		    $data['view_file'] = 'home_page';
		    $this->template->front($data);
		}
		function get_supplier_documents()
		{
			$supplier_type=$this->input->post('supplier_type');
			$supplier_id=$this->session->userdata['supplier']['supplier_id'];
			$data['doc'] = Modules::run('front/_get_specific_table_with_pagination_and_where',array("assign_to"=>"supplier","status"=>"1","supplier_type"=>"0"), "level asc","document","id,doc_name,level,supplier_type","","",array("supplier_type"=>$supplier_type),"","")->result_array();
			if(!empty($data['doc'])){
				foreach($data['doc'] as $key => $value)
				{
					$data['doc'][$key]['document']="";
					$uploaded = Modules::run('ingredients/_get_data_from_db_table',array("s_doc_id"=>$value['id'],"supplier_id"=>$supplier_id),"supplier_documents","","","id,doc_name,document,expiry_date","")->row_array();
					if(isset($uploaded['document']))
					$data['doc'][$key]['document']= $uploaded['document'];
					$data['doc'][$key]['expiry_date']= "";
					if(isset($uploaded['expiry_date']))
					$data['doc'][$key]['expiry_date']= date("m/d/Y", strtotime($uploaded['expiry_date']));
					$data['doc'][$key]['supplier_type_name']="";
					if($value['supplier_type']!="0"){
					$type = Modules::run('ingredients/_get_data_from_db_table',array("id"=>$value['supplier_type']),"supplier_type","","","id,name","")->row_array();
					$data['doc'][$key]['supplier_type_name']=$type['name'];
					}
				} 
			}
		    $this->load->view('documents_view',$data);
		}
		
		// function get_doc_name()
		// {
		// 	$doc=array();
		// 	$type_id=$this->input->post('type_id');
		// 	$supplier_id=$this->session->userdata['supplier']['supplier_id'];
		// 	$ingredients = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET.'_ingredients_supplier',"","","ingredient_id,s_item_name","")->result_array();
		// 	foreach($ingredients as $ingrdnt => $ing)
		// 	{
		// 		foreach($type_id as $key => $value)
		// 		{
		// 			$result = Modules::run('ingredients/_get_data_from_db_table',array("type_id"=>$value,"assign_to"=>"ingredient","status"=>"1"),'document',"","","doc_name,id","")->result_array();
		// 			foreach($result as $keys => $values){
		// 				$query=$this->get_ingredients_data($values['id'],$supplier_id)->row_array();
		// 				$doc['document']=null;
		// 				if(!empty($query) && isset($query['document']))
		// 				$doc['document']=$query['document'];
		// 				$doc['doc_name']=$values['doc_name'];
		// 				$doc['ing_id']=$ing['ingredient_id'];
		// 				$doc['ing_name']=$ing['s_item_name'];
		// 				$temp[]=$doc;
		// 			}
		// 		}
		// 	}
		// 	$doc=$temp;
		// 	header('Content-Type: application/json');
		// 	echo json_encode(array("doc"=>$doc));
		// }
		function update_password()
		{
			$old_password=$this->input->post('old_password');
			$password=$this->input->post('password');
			$c_password=$this->input->post('c_password');
			if(!empty($old_password) && !empty($password) && !empty($c_password)){
				$supplier_id=$this->session->userdata['supplier']['supplier_id'];
				$old_password=md5($old_password);
				$validate= Modules::run('api/_get_specific_table_with_pagination',array("password" =>$old_password,"supplier_id"=> $supplier_id),'id desc','supplier_account','id','1','1')->num_rows();
				if($validate > 0)
				{
					$password=md5($password);
					Modules::run('api/update_specific_table',array("supplier_id"=>$supplier_id),array("password"=>$password),'supplier_account');
					$status=TRUE;
					$message="Password Updated ";
				}
				else{
					$status=FALSE;
					$message="Wrong Password ";
				}
			}
			else
			{
				$status=FALSE;
				$message="Please Provide all required Information";
			}
			echo '<article><status>'.$status.'</status><message>'.$message.'</message><article>';
		}
		function profile_update()
		{
			$data['name']=$this->input->post('name');
			$data['email']=$this->input->post('email');
			$data['phone_no']=$this->input->post('phone');
			$data['city']=$this->input->post('city');
			$data['country']=$this->input->post('country');
			$data['state']=$this->input->post('state');
			$data['address']=$this->input->post('address');
			if(!empty($data['name']) && !empty($data['email']) && !empty($data['phone_no']) && !empty($data['city']) && !empty($data['country']) && !empty($data['state'])){
				$supplier_id=$this->session->userdata['supplier']['supplier_id'];
				$password=md5($password);
				Modules::run('api/update_specific_table',array("id"=>$supplier_id),$data,'supplier');
				$status=TRUE;
				$message="Profile Updated ";
			}
			else
			{
				$status=FALSE;
				$message="Please Provide all required Information";
			}
			echo '<article><status>'.$status.'</status><message>'.$message.'</message><article>';
		}

		function ingredient_location()
		{
			$supplier_id=$this->session->userdata['supplier']['supplier_id'];
			$ing_id=$this->input->post('ing_id');
			$loc=$this->input->post('loc');
			$total=count($ing_id);
			if(isset($ing_id) && !empty($ing_id)){
				for ($i=0; $i < $total; $i++) {
					$where_attr['supplier_id']=$supplier_id;
					$where_attr['ingredient_id']=$ing_id[$i];
					$data['ing_loc']=$loc[$i];
					Modules::run('api/update_specific_table',$where_attr,$data,DEFAULT_OUTLET.'_ingredients_supplier');
					}	
					$status=TRUE;
					$message="Location Saved";
				}
			else
			{
				$status=FALSE;
				$message="Please Provide all required Information";
			}
			echo '<article><status>'.$status.'</status><message>'.$message.'</message><article>';
		}
		function login() {
		    $this->load->module('template');
		    $data['header_file'] = 'header-login';
		    $data['view_file'] = 'login';
		    $this->template->front($data);
		}
		function logout(){
			$supplier_id=$this->session->userdata['supplier']['supplier_id'];
			$this->session->unset_userdata('supplier');
			redirect(BASE_URL.'login/'.$supplier_id);
		}
		function submit_login()
		{
		    $this->load->library('form_validation');
    		$this->form_validation->set_rules('txtUserName', 'Username', 'required|trim|xss_clean');
    		$this->form_validation->set_rules('txtPassword', 'Passwords', 'required|trim|xss_clean|callback_pword_check');
    		$username = $this->input->post('txtUserName', TRUE);
    		$id = $this->input->post('id', TRUE);
    		$password = md5($this->input->post('txtPassword', TRUE)); 
    		$row = Modules::run('ingredients/_get_data_from_db_table',array("username"=>$username,"password"=>$password,"supplier_id"=>$id),"supplier_account","","","*","")->row();
    		if (empty($row)) {
    			redirect(BASE_URL.'login/'.$id);
    			exit();
    		}
    		$data['user_id'] = $row->id;
    		$data['user'] = $row->username;
    		$data['supplier_id'] = $id;
    		$this->session->set_userdata('supplier', $data);
    		$supplier = $this->session->userdata('supplier');
    		Modules::run('api/update_specific_table',array("id"=>$row->id),array("login_status"=>'1'),'supplier_account');
    		redirect(BASE_URL.'index');
		}
		
		function thanks() {
			$count = $this->session->flashdata('count');
		    $supplier_id=$this->session->userdata['supplier']['supplier_id'];
			$detail= Modules::run('ingredients/_get_data_from_db_table',array("id"=>$supplier_id),"supplier","","","*","")->row_array();
			$submitted = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),"supplier_documents","","","*","")->num_rows();
			$submitted_ing = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET."_ingredients_document","","","*","")->num_rows();
			$total_doc = Modules::run('ingredients/_get_data_from_db_table',array("assign_to"=>"supplier","status"=>"1"),"document","","","*","")->num_rows();
			$submitted=$submitted+$submitted_ing;
			$total_doc=$total_doc+$count;
			if($submitted==$total_doc)
				$data['message']= $detail['name']." you have completed your profile by providing all your documents.";
			else
				$data['message']= $detail['name']." you have submitted ".$submitted." out of ".$total_doc." documents.";
			$data['supplier_id']=$supplier_id;
		    $this->session->unset_userdata('supplier');
		    $this->load->module('template');
		    $data['header_file'] = 'header-login';
		    $data['view_file'] = 'thanks';
		    $this->template->front($data);
		}
		
		function submit_doc(){
			$supplier_id=$this->session->userdata['supplier']['supplier_id'];
			$supplier_type=$this->input->post('supplier_type');
			$update_id=$this->input->post('id');
			Modules::run('api/insert_or_update',array("id"=>$supplier_id),array("supplier_type"=>$supplier_type),'supplier');
			$doc = Modules::run('front/_get_specific_table_with_pagination_and_where',array("assign_to"=>"supplier","status"=>"1","supplier_type"=>"0"), "level asc","document","id,doc_name,level,supplier_type","","",array("supplier_type"=>$supplier_type),"","")->result_array();
			if (is_numeric($update_id) && $update_id != 0) {
                    if(!empty($doc)){
                        $where['id'] = $update_id;
                        foreach($doc as $key => $value){
						$exp_date=$this->input->post("expiry_date_$key");
                        if(isset($_FILES["news_main_page_file_$key"]['size']) )
                            if($_FILES["news_main_page_file_$key"]['size'] > 0) {
                                $itemInfo = Modules::run('supplier/_get_by_arr_id',$where)->row();
                                if(isset($itemInfo->document) && !empty($itemInfo->document)) 
                                    Modules::run('supplier/delete_images_by_name',SUPPLIER_DOCUMENTS_PATH,$itemInfo->document);
                                    Modules::run('supplier/delete_from_table',array("s_doc_id"=>$value['id'],"supplier_id"=>$update_id),"supplier_documents");
									Modules::run('supplier/upload_dynamic_image',SUPPLIER_DOCUMENTS_PATH,$update_id,"news_main_page_file_$key",'document','id','supplier_documents',$value['id'],$value['doc_name'],$exp_date);
									
							}
							if(!empty($exp_date)){
							    $exp_date=date("Y-m-d", strtotime($exp_date));
								Modules::run('api/insert_or_update',array("supplier_id"=>$supplier_id,"s_doc_id"=>$value['id']),array("expiry_date"=>$exp_date),'supplier_documents');
						}
					  } 
					}
					// $type_selected=$this->supplier_ingredients($supplier_id)->result_array();
					// Modules::run('ingredients/delete_from_table',array("ingredient_id"=>$update_id),DEFAULT_OUTLET.'_assigned_ingredient_types');
					// $selected_type= $this->input->post('type');
					// if(!empty($selected_type)){
					// 	 foreach($selected_type as $key => $value)
					// 	{
					// 		$type['ingredient_id']=$update_id;
					// 		$type['type_id']=$value;
					// 		Modules::run('ingredients/_insert_data',$type,DEFAULT_OUTLET.'_assigned_ingredient_types');
					// 	}
					// }
					$count=$this->submit_ingredient_document($supplier_id);
				}
			$detail= Modules::run('ingredients/_get_data_from_db_table',array("id"=>$supplier_id),"supplier","","","*","")->row_array();
			$submitted = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),"supplier_documents","","","*","")->num_rows();
			//$submitted_ing = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET."_ingredients_document","","","*","")->num_rows();
			$total_doc =  Modules::run('front/_get_specific_table_with_pagination_and_where',array("assign_to"=>"supplier","status"=>"1","supplier_type"=>"0"), "level asc","document","id,doc_name,level,supplier_type","","",array("supplier_type"=>$supplier_type),"","")->num_rows();
			//$submitted=$submitted+$submitted_ing;
			//$total_doc=$total_doc+$count;
			if($submitted==$total_doc)
				$message= $detail['name']." you have completed your profile by providing all your documents.";
			else
				$message= $detail['name']." you have submitted ".$submitted." out of ".$total_doc." documents.";
			$this->session->set_flashdata('status', 'success');
			$this->session->set_flashdata('message', $message);

    	    redirect(BASE_URL . 'index#supplier_documents');
		}
		function submit_ingredient_document($supplier_id)
		{
			$ing_doc=$temp=array();
			$ingredients = Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id),DEFAULT_OUTLET.'_ingredients_supplier',"","","ingredient_id,s_item_name","")->result_array();
			foreach($ingredients as $ingrdnt => $ing)
			{
				$type_selected=$this->supplier_ingredients($supplier_id,$ing['ingredient_id'])->result_array();
				foreach($type_selected as $key => $value)
				{
					$result = Modules::run('ingredients/_get_data_from_db_table',array("type_id"=>$value['type_id'],"assign_to"=>"ingredient","status"=>"1"),'document',"","","doc_name,id","")->result_array();
					foreach($result as $keys => $values){
						$query=Modules::run('ingredients/_get_data_from_db_table',array("supplier_id"=>$supplier_id,"ingredient_id"=>$ing['ingredient_id'],"document_id"=>$values['id']),DEFAULT_OUTLET.'_ingredients_document',"","","document","")->row_array();
						$ing_doc['ing_id']=$ing['ingredient_id'];
						$ing_doc['doc_id']=$values['id'];
						$temp[]=$ing_doc;
					}
					
				}
			}
			foreach($temp as $key => $value)
			{
				$where['ingredient_id']=$value['ing_id'];
				$where['document_id']=$value['doc_id'];
				$where['supplier_id']=$supplier_id;
				if(isset($_FILES["main_file_$key"]['size']) )
					if($_FILES["main_file_$key"]['size'] > 0) {
						$itemInfo = Modules::run('ingredients/_get_data_from_db_table',$where,DEFAULT_OUTLET.'_ingredients_document',"","","*","")->row();
						if(isset($itemInfo->document) && !empty($itemInfo->document)) 
							Modules::run('ingredients/delete_images_by_name',INGREDIENT_DOCUMENTS_PATH,$itemInfo->document);
							Modules::run('ingredients/delete_from_table',$where,DEFAULT_OUTLET.'_ingredients_document');
							Modules::run('ingredients/upload_dynamic_image',INGREDIENT_DOCUMENTS_PATH,$value['ing_id'],'main_file_'.$key,'document','id',DEFAULT_OUTLET.'_ingredients_document',$value['doc_id'],$supplier_id);
					}
			}
			$count=count($temp);
			return $count;
		}

		function supplier_ingredients($supplier_id,$ing_id)
		{
			$this->load->model('mdl_front');
			return  $this->mdl_front->supplier_ingredients($supplier_id,$ing_id);
		}
		function _get_specific_table_with_pagination_and_where($cols, $order_by,$table,$select,$page_number,$limit,$or_where='',$and_where='',$having=''){
            $this->load->model('mdl_front');
            $query = $this->mdl_front->_get_specific_table_with_pagination_and_where($cols, $order_by,$table,$select,$page_number,$limit,$or_where,$and_where,$having);
            return $query;
        }
}