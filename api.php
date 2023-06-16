<?php
	
	require_once("Rest.inc.php");
	include("apiModel.php");
	
	class API extends REST {
	
		public $data = "";

		
		public function __construct()
		{
			parent::__construct();				// Init parent contructor
	
		}
		
 
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			
			if((int)method_exists($this,$func) > 0)
			{
				$this->$func();
			}
			else
			{
				$this->response('',404);	// If the method not exist with in this class, response would be "Page not found".
			}
		}
		

		
		private function getdata()
		{	
				$success_prod = array();
				$fail_prod = array();	

				$success_cat = array();
				$fail_cat = array();

				$success_prodcat = array();
				$fail_prodcat = array();				  

				$result = file_get_contents('php://input');
				$data = json_decode($result, TRUE);
				require '../wp-load.php';
				global $wpdb;				  
				//check admin login
				$result_adm = apiModel::chkAdmin($wpdb); 

				if(count($result_adm)>0){

				  if(count($data['products'])>0){
					  
				    for($p=0;$p<count($data['products']);$p++){

						$result = apiModel::findProduct($wpdb,$data['products'][$p]['id'],$data['products'][$p]['default_category_id']);

						if(count($result)>0){
							$return = apiModel::updateProduct($wpdb,$data['products'][$p]['id'],$data['products'][$p]['name'],
							$data['products'][$p]['logo_url'],$data['products'][$p]['vendor_name'],
							$data['products'][$p]['slug'],$data['products'][$p]['popularity'],$data['products'][$p]['default_category_id'],$data['products'][$p]['industry']);
							
						}else{
							$return = apiModel::insertProduct($wpdb,$data['products'][$p]['id'],$data['products'][$p]['name'],
							$data['products'][$p]['logo_url'],$data['products'][$p]['vendor_name'],
							$data['products'][$p]['slug'],$data['products'][$p]['popularity'],$data['products'][$p]['default_category_id'],$data['products'][$p]['industry']);

						}
						if($return=='done'){
							$success_prod[] = 1;
						}else{
							$fail_prod[] = 1;
						}						
						
					}
					  
					// $outputdata = array( 'Product_Entry' => 'Successfull insert product(s):'.count($success_prod).', Fail to insert product(s):'.count($fail_prod));
					// $this->response($this->json($outputdata), 200); 
					  
				  } 
				  
				  if(count($data['categories'])>0){
                       
				    for($c=0;$c<count($data['categories']);$c++){
						
						/* categories */
						  $result = apiModel::findCategory($wpdb,$data['categories'][$c]['id']); 
						  				  
						  
						 if($result=="bothcat"){
							$return = apiModel::updateCategoryBoth($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);							
							
						}elseif($result=="mycat"){
							$return = apiModel::updateCategoryMy($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);	
							$return = apiModel::insertCategoryWp($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);							
							
						}elseif($result=="wpcat"){
							$return1 = apiModel::updateCategoryWp($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);
                            $return2 = apiModel::insertCategory($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);	
							if($return1=='done' && $return2=='done'){
								$return = 'done';
							}else{
								$return = 'fail';
							}
							
						}else{
							$return = apiModel::insertCategory($wpdb,$data['categories'][$c]['id'],$data['categories'][$c]['name'],
							$data['categories'][$c]['slug'],$data['categories'][$c]['position']);
							

						}
						
						
						if($return=='done'){
							$success_cat[] = 1;
						}else{
							$fail_cat[] = 1;
						} 						
						
					}

					// $outputdata_cat = array( 'Category_Entry' => 'Successfull insert category(s):'.count($success_cat).', Fail to insert category(s):'.count($fail_cat));
					// $this->response($this->json($outputdata_cat), 200); 
					 
					  
				  }	

 				  if(count($data['product_categories'])>0){

				    for($pc=0;$pc<count($data['product_categories']);$pc++){

						$result = apiModel::findProductCategory($wpdb,$data['product_categories'][$pc]['id']);

						if(count($result)>0){
							$return = apiModel::updateProductCategory($wpdb,$data['product_categories'][$pc]['id'],$data['product_categories'][$pc]['product_id'],
							$data['product_categories'][$pc]['category_id']);
						}else{
							$return = apiModel::insertProductCategory($wpdb,$data['product_categories'][$pc]['id'],$data['product_categories'][$pc]['product_id'],
							$data['product_categories'][$pc]['category_id']);
							

						}
						if($return=='done'){
							$success_prodcat[] = 1;
						}else{
							$fail_prodcat[] = 1;
						}						
						
					}

				  }	 
 				     $outputdata[] = array( 'Product_Entry' => 'Successfull insert product(s):'.count($success_prod).', Fail to insert product(s):'.count($fail_prod));
                     $outputdata[] = array( 'Category_Entry' => 'Successfull insert category(s):'.count($success_cat).', Fail to insert category(s):'.count($fail_cat));
					 $outputdata[] = array( 'Product_Category_Entry' => 'Successfull insert product category relation:'.count($success_prodcat).', Fail to insert product category relation:'.count($fail_prodcat));
					 $this->response($this->json($outputdata), 200);  

				}else{
                     $outputdata[] = array( 'Authorization' => 'Authorization Failed');
					 $this->response($this->json($outputdata), 401); 
				}					
				  
				  
		}



		private function review(){
			require '../wp-load.php';
			global $wpdb;
			$result = file_get_contents('php://input');
			$data = json_decode($result, TRUE);
			$prodId = $data[0]['value'];	
			$review = $data[1]['value'];	
			$company = $data[2]['value'];	
			$returnChk = apiModel::insertReview($wpdb,$prodId,$company,$review);
			if($returnChk=="done"){
				echo 'save';
			}else{
				echo 'fail';
			}
			
		}


		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				//return json_encode($data, JSON_UNESCAPED_SLASHES);
				return json_encode($data);
			}
		}
		
		
	}
	
	// Initiiate Library
	$api = new API;
	$api->processApi();
	
?>