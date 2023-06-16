<?php

class apiModel
{
	
	public $productname;
	public $logo_url;
	public $vendor_name;
	public $productslug;
	public $popularity;
	public $default_category_id;
	public $industry;
	public $categoryname;
	public $categoryslug;
	public $position;
	public $product_id;
	public $category_id;	
	
	
    function __construct($productname,$logo_url,$vendor_name,$productslug,$popularity,$default_category_id,$industry,$categoryname,$categoryslug,$position,$product_id,$category_id)
    {

		$this->productname = $productname;
		$this->logo_url = $logo_url;
		$this->vendor_name = $vendor_name;
		$this->productslug = $productslug;
		$this->popularity = $popularity;
		$this->default_category_id = $default_category_id;
		$this->industry = $industry;
		$this->categoryname = $categoryname;
		$this->categoryslug = $categoryslug;
		$this->position = $position;
		$this->product_id = $product_id;
		$this->category_id = $category_id;

    }

    // find product
    public static function chkAdmin($wpdb){
		
		return $result = $wpdb->get_results ("SELECT id FROM  wp_login");	
	}    


    // find product
    public static function findProduct($wpdb,$product_id,$default_category_id){
        require '../wp-load.php';
		global $wpdb;		
		return $result = $wpdb->get_results ("SELECT id FROM  wp_products WHERE id =  $product_id and default_category_id = $default_category_id");	
	}
    // insert product
    public static function insertProduct($wpdb,$product_id,$productname,$logo_url,$vendor_name,$productslug,$popularity,$default_category_id,$industry){
		require '../wp-load.php';
		global $wpdb;
		try
		{ $wpdb->insert('wp_products', array(
				'id' => $product_id,
				'name' => $productname,
				'logo_url' => $logo_url,
				'vendor_name' => $vendor_name,
				'logo_url' => $logo_url,
				'slug' => $productslug,
				'popularity' => $popularity,
				'default_category_id' => $default_category_id,
				'industry' => $industry	
			));

			return 'done';
	    }
		catch (Exception $e)
		{
			return $e;
			//throw $e;
		}

	}
        //update product
    public static function updateProduct($wpdb,$product_id,$productname,$logo_url,$vendor_name,$productslug,$popularity,$default_category_id,$industry){
		require '../wp-load.php';
		global $wpdb;
		try
		{ 
		$sql = "UPDATE `wp_products` 
		    SET name = '".$productname."',
				logo_url = '".$logo_url."',
				vendor_name = '".$vendor_name."',
				logo_url = '".$logo_url."',
				slug = '".$productslug."',
				popularity = ".$popularity.",
				industry = '".$industry."' WHERE id=".$product_id." and default_category_id=".$default_category_id;
			$wpdb->query($sql);	
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		}

	}


    // find categories
    public static function findCategory($wpdb,$category_id){

		$result = $wpdb->get_results ("SELECT id FROM  wp_categories WHERE id =  $category_id");	
		$wp_result = $wpdb->get_results ("SELECT term_id FROM  wp_terms WHERE term_id =  $category_id");
		if(count($result)>0 &&  count($wp_result)>0){return "bothcat";}
		if(count($result)>0 &&  count($wp_result)==0){return "mycat";}
		if(count($result)==0 &&  count($wp_result)>0){return "wpcat";}
	}
    // insert categories
    public static function insertCategory($wpdb,$category_id,$categoryname,$categoryslug,$position){

 		try
		{ 
		    $wpdb->insert('wp_categories', array(
				'id' => $category_id,
				'name' => $categoryname,
				'slug' => $categoryslug,
				'position' => $position	
			));	
			
			$result_term = $wpdb->get_results ("SELECT term_id FROM  wp_terms WHERE term_id =  $category_id");
			if(count($result_term)==0){
		    $wpdb->insert('wp_terms', array(
				'term_id' => $category_id,
				'name' => $categoryname,
				'slug' => $categoryslug,
				'position' => $position	
			));
			}elseif(count($result_term)>0){
			   $sqlwp = "UPDATE `wp_terms` 
				SET name = '".$categoryname."',
					slug = '".$categoryslug."',
					position = ".$position." WHERE term_id=".$category_id;
			   $wpdb->query($sqlwp);

			}			
			$result = $wpdb->get_results ("SELECT term_id FROM  wp_term_taxonomy WHERE term_id =  $category_id");
			if(count($result)==0){
		    $wpdb->insert('wp_term_taxonomy', array(
				'term_taxonomy_id' => $category_id,
				'term_id' => $category_id,
				'taxonomy' => 'product_category',
				'description' => $categoryname,
				'parent' => 0,
				'count' => 0				
			));
			}elseif(count($result)>0){
				$sqlwp = "UPDATE `wp_term_taxonomy` 
				SET taxonomy = 'product_category',
					description = '".$categoryname."'
					WHERE term_id=".$category_id;
				$wpdb->query($sqlwp);

			}
			
			
			
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			throw $e;
		} 

	}
 
public static function insertCategoryMy($wpdb,$category_id,$categoryname,$categoryslug,$position){

		try
		{ 
		    $wpdb->insert('wp_categories', array(
				'id' => $category_id,
				'name' => $categoryname,
				'slug' => $categoryslug,
				'position' => $position	
			));
		    $wpdb->insert('wp_terms', array(
				'term_id' => $category_id,
				'name' => $categoryname,
				'slug' => $categoryslug,
				'position' => $position	
			));	
		    $wpdb->insert('wp_term_taxonomy', array(
				'term_taxonomy_id' => $category_id,
				'term_id' => $category_id,
				'taxonomy' => 'product_category',
				'description' => $categoryname,
				'parent' => 0,
				'count' => 0,				
			));			
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		} 

	}

public static function insertCategoryWp($wpdb,$category_id,$categoryname,$categoryslug,$position){
 
		try
		{ 
		    $wpdb->insert('wp_terms', array(
				'term_id' => $category_id,
				'name' => $categoryname,
				'slug' => $categoryslug,
				'position' => $position	
			));	
		    $wpdb->insert('wp_term_taxonomy', array(
				'term_taxonomy_id' => $category_id,
				'term_id' => $category_id,
				'taxonomy' => 'product_category',
				'description' => $categoryname,
				'parent' => 0,
				'count' => 0				
			));			
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			throw $e;
		} 

	}

 //update categories
    public static function updateCategoryBoth($wpdb,$category_id,$categoryname,$categoryslug,$position){

		try
		{ 
		   $sql = "UPDATE `wp_categories` 
		    SET name = '".$categoryname."',
				slug = '".$categoryslug."',
				position = ".$position." WHERE id=".$category_id;
		   $wpdb->query($sql);
		   $sqlwp = "UPDATE `wp_terms` 
		    SET name = '".$categoryname."',
				slug = '".$categoryslug."',
				position = ".$position." WHERE term_id=".$category_id;
		   $wpdb->query($sqlwp);
            
			$result = $wpdb->get_results ("SELECT term_id FROM  wp_term_taxonomy WHERE term_id =  $category_id");
			if(count($result)==0){
		    $wpdb->insert('wp_term_taxonomy', array(
				'term_taxonomy_id' => $category_id,
				'term_id' => $category_id,
				'taxonomy' => 'product_category',
				'description' => $categoryname,
				'parent' => 0,
				'count' => 0				
			));
			}elseif(count($result)>0){
				$sqlwp = "UPDATE `wp_term_taxonomy` 
				SET taxonomy = 'product_category',
					description = '".$categoryname."'
					WHERE term_id=".$category_id;
				$wpdb->query($sqlwp);

			}				


		   
		   return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		} 

	}

        //update categories
    public static function updateCategoryMy($wpdb,$category_id,$product_id,$categoryslug,$position){

 		try
		{ 
		   $sql = "UPDATE `wp_categories` 
		    SET name = '".$categoryname."',
				slug = '".$categoryslug."',
				position = ".$position." WHERE id=".$category_id;
		$wpdb->query($sql);			
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		} 

	}
        //update categories
    public static function updateCategoryWp($wpdb,$category_id,$categoryname,$categoryslug,$position){

 		try
		{ 
		    $sql = "UPDATE `wp_terms` 
		    SET name = '".$categoryname."',
				slug = '".$categoryslug."',
				position = ".$position." WHERE term_id=".$category_id;
		    $wpdb->query($sql);	

             $result = $wpdb->get_results ("SELECT term_id FROM  wp_term_taxonomy WHERE term_id =  $category_id");
			if(count($result)==0){
		    $wpdb->insert('wp_term_taxonomy', array(
				'term_taxonomy_id' => $category_id,
				'term_id' => $category_id,
				'taxonomy' => 'product_category',
				'description' => $categoryname,
				'parent' => 0,
				'count' => 0				
			));
			}elseif(count($result)>0){
				$sqlwp = "UPDATE `wp_term_taxonomy` 
				SET taxonomy = 'product_category',
					description = '".$categoryname."'
					WHERE term_id=".$category_id;
				$wpdb->query($sqlwp);

			}

			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		} 

	}


   // find categories
    public static function findProductCategory($wpdb,$productcategory_id){
		return $result = $wpdb->get_results ("SELECT id FROM  wp_product_categories WHERE id =  $productcategory_id");	
	}
    // insert categories
    public static function insertProductCategory($wpdb,$productcategory_id,$product_id,$category_id){

		try
		{ $wpdb->insert('wp_product_categories', array(
				'id' => $productcategory_id,
				'product_id' => $product_id,
				'category_id' => $category_id
				
			));
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		}

	}
        //update categories
    public static function updateProductCategory($wpdb,$productcategory_id,$product_id,$category_id){

		try
		{ 
		   $sql = "UPDATE `wp_product_categories` 
		    SET product_id = ".$product_id.",
				category_id = ".$category_id."
				WHERE id=".$productcategory_id;
		$wpdb->query($sql);			
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		}

	} 


   // find categories
    public static function findProducts($wpdb,$category_id,$filter){
		if($filter<>""){
			return $result = $wpdb->get_results ("SELECT * FROM  wp_products WHERE default_category_id =  $category_id and industry= '$filter'");
		}else{
			return $result = $wpdb->get_results ("SELECT * FROM  wp_products WHERE default_category_id =  $category_id");	
		}
	}
    public static function findProductsPagination($wpdb,$category_id,$page_first_result,$results_per_page,$filter){
		if($filter<>""){
			return $result = $wpdb->get_results ("SELECT * FROM  wp_products WHERE default_category_id =  $category_id and industry= '$filter' LIMIT $page_first_result,$results_per_page");
		}else{
			return $result = $wpdb->get_results ("SELECT * FROM  wp_products WHERE default_category_id =  $category_id LIMIT $page_first_result,$results_per_page");
		}			
	}	




    public static function popularitysort($wpdb){
		return $result = $wpdb->get_results ("SELECT * FROM  wp_products WHERE default_category_id =  $category_id LIMIT $page_first_result,$results_per_page");	
	}
    public static function industryFilter($wpdb,$category_id){
		return $result = $wpdb->get_results ("SELECT industry FROM  wp_products WHERE default_category_id =  $category_id order by industry");	
	}


    public static function insertReview($wpdb,$prod_id,$company,$review){
		
		try
		{ $wpdb->insert('wp_product_review', array(
				'product_id' => $prod_id,
				'company' => $company,
				'review' => $review				
			));
			return 'done';
	    }
		catch (Exception $e)
		{
			
			return $e;
			//throw $e;
		}
	}



}

?>
