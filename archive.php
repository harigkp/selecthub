<?php
   /**
    * The template for displaying Archive pages.
    *
    * Learn more: http://codex.wordpress.org/Template_Hierarchy
    *
    * @package Elixar
    */
   
   get_header(); ?>
<!-- Archive Breadcrumbs -->
<!--<div class="e-breadcrumb-page-title">
   <div class="container">
   	<div class="row">
   		<div class="col-sm-6">
   			<?php //the_archive_title( '<h1 class="e-page-title text-center-xs">', '</h1>' );
      //the_archive_description( '<div class="archive-description">', '</div>' );?>
   		</div>
   		<div class="col-sm-6">
   			<?php //if (function_exists('elixar_breadcrumbs')) elixar_breadcrumbs(); ?>
   		</div>
   	</div>
   </div>
   </div>-->
   
<?php
	/* Data of category and its single post*/
	$category = get_queried_object();
	$descriptionDESC = $category->description;
	$args = array(
		'post_type' => 'product_listing',
		'category'    => $category->name,
	);
	$loop = new WP_Query( $args );   
 ?>    
 <?php 
   global $wpdb;
   
   include("api/apiModel.php");
   /* Product search */
   if (isset ($_GET['psearch']) ) {  
   	$search = $_GET['psearch'];
   } else {  
   	$search = '';
   }   
    /* Sorted by popularity */
   if (isset ($_GET['popularity']) ) {  
   	$popularity = $_GET['popularity'];
   } else {  
   	$popularity = '';
   }				
   /* pagination */
   if (!isset ($_GET['page']) ) {  
   	$page = 1;  
   } else {  
   	$page = $_GET['page'];  
   } 
    /* Filter by industry */
   if (isset ($_GET['industry']) && $_GET['industry']<>"No filter applied" ) {  
   	$filter = $_GET['industry'];  
   } else {  
   	$filter = '';
   } 
   
     $results_per_page = 6;  
              $page_first_result = ($page-1) * $results_per_page; 
     
   
   $result = apiModel::findProducts($wpdb,$category->term_id,$filter,$search,$popularity);
   $pageshow=false;
   if(count($result)>=6){
   	$pageshow=true;
   }
   
   $result_page = apiModel::findProductsPagination($wpdb,$category->term_id,$page_first_result,$results_per_page,$filter,$search,$popularity);
   
   $number_of_page = ceil (count($result) / $results_per_page);  
   
   $resultFilter = apiModel::industryFilter($wpdb,$category->term_id);
              
    ?>  
<div class="container">
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a class="black-text" href="#">Categories</a>&nbsp;&nbsp;<i
         class="fa fa-angle-right" aria-hidden="true"></i>
      </li>
      <li class="breadcrumb-item active">&nbsp;&nbsp;<?php echo $category->name;?></li>
   </ol>
</div>
<section>
   <div class="container">
      <div class="row">
         <div class="col-md-6">
            <h1 class="jumbotron-heading"><?php if ( $loop->have_posts() ) : the_title(); endif;?></h1>
            <p><?php echo $descriptionDESC;?>
            </p>
			<?php if(count($result_page)>0){?>
            <form id="psearchID" autocomplete="off" method="get" role="search">
               <div class="form-group">
                  <input type="text" name="psearch" id="psearch" class="form-control inputfield" 
                     placeholder="search for product...">
               </div>
            </form>
			<?php } ?>
         </div>
      </div>
   </div>
</section>
<!-- GRIDS -->
<div class="elixar-blog">

<main role="main">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <?php if(count($result_page)>0){?>
            <div class="col-md-2" id="filterby" style="float:right">
               <fieldset class="border" >
                  <legend class ='text-center'>Sorted by</legend>
                  <select class="form-select" id="popularitySORT" aria-label="Default select example">
                     <option <?php if(($popularity<>"" && $popularity=="popularity") || ($popularity=="")){?> selected <?php } ?> value="popularity">Popularity</option>
                     <option <?php if($popularity<>"" && $popularity=="A-Z"){?> selected <?php } ?> value="A-Z">A-Z</option>
                     <option <?php if($popularity<>"" && $popularity=="Z-A"){?> selected <?php } ?> value="Z-A">Z-A</option>
                  </select>
               </fieldset>
            </div>
            <div class="col-md-2" id="filterby" style="float:right">
               <fieldset class="border" >
                  <legend class ='text-center'>Filter by</legend>
                  <select class="form-select" id="filterbyindustry" aria-label="Default select example">
                     <option selected>No filter applied</option>
                     <?php for($i=0;$i<count($resultFilter);$i++){?>
                     <option <?php if($filter<>"" && $filter==$resultFilter[$i]->industry){?> selected <?php } ?> value="<?php echo $resultFilter[$i]->industry ?>"><?php echo $resultFilter[$i]->industry ?></option>
                     <?php } ?>
                  </select>
               </fieldset>
            </div>
            <?php } ?>				
         </div>
         <div class="col-md-12">&nbsp;</div>
         <?php if(count($result_page)>0){?>
         <?php for($i=0;$i<count($result_page);$i++){?>
         <div class="col-md-2">
            <div class="card mb-2 shadow-sm">
               <img class="img-box" alt="Thumbnail [100%x225]" src="<?php echo site_url();?>/wp-content/uploads/2023/06/product.jpg">							
               <div class="card-body align-items-center">
                  <h6 class="custom-h6"><a href="#" class="mycustom" id="<?php echo $result_page[$i]->id;?>" data-toggle="modal" data-target="#myModal"><?php echo $result_page[$i]->name;?></a></h6>
                  <strong>
                     <p class="strong-p"><?php echo $result_page[$i]->vendor_name;?></p>
                  </strong>
               </div>
            </div>
         </div>
         <?php } ?>	
      </div>
      <div class="col-md-12">&nbsp;</div>
      <?php if($pageshow){ ?>
      <nav id="page">
         <ul class="pagination pagination-sm justify-content-center">
            <?php for($page = 1; $page<= $number_of_page; $page++) {  ?>
            <li class="page-item <?php if(isset($_GET['page']) && $page == $_GET['page']){?>disabled<?php } else {?>enable<?php } ?>">
               <a class="page-link" href="<?php echo site_url();?>/product-category/business-intelligence/?page=<?php echo $page?>"><?php echo $page?></a>
            </li>
            <?php } ?>
         </ul>
      </nav>
      <?php } ?>
      <?php }else{ ?>
      <center><span style="color:red;"><strong>Product not found</strong></span></center>
	  <div class="col-md-12">&nbsp;</div>
      <?php } ?>
      <div class="col-md-12">&nbsp;</div>
	  <?php if ( $loop->have_posts() ) : the_content(); endif;?>
 
   </div>
</main>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add review</h4>
         </div>
         <div class="modal-body" id="afterSend">
            <form class="cmxform" id="reviewFRM" action="" >
               <input type="hidden" value="" name="prodId" id="prodId" />
               <div class="mb-3">
                  <label for="review" class="col-form-label">Review:</label>
                  <textarea name="review" class="form-control" id="review" required></textarea>
                  <p id="errorReview" style="color:red; display:none">Please enter review.</p>
               </div>
               <div class="mb-3">
                  <label for="company" class="col-form-label">Company name:</label>
                  <input type="text" name="company"  id="company" class="form-control" required>
                  <p id="errorCompany" style="color:red; display:none">Please enter company name.</p>
               </div>
         </div>
         <center><img style="display:none;" id="load" src="<?php echo site_url();?>/wp-content/uploads/2023/06/ajax-loader.gif"></center>
         <center><div style="display:none;color:green;" id="saved">Record saved successfully.</div></center>
         <center><div style="display:none;color:red" id="failed">Record not saved, please try after some time.</div></center>
         <div class="modal-footer">
         <button type="submit" id="saveBtn" class="btn btn-primary">Save</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
         </div>
         </form>
      </div>
   </div>
</div>
<script src="<?php bloginfo('template_directory'); ?>/js/lib/jquery.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/dist/jquery.validate.js"></script>
<script>
   $("#filterbyindustry").change(function(){
     window.location = "<?php echo site_url();?>/product-category/business-intelligence/?<?php if(isset($_GET['page'])){?>page=<?php echo $_GET['page'];?>&<?php } ?>industry="+this.value;
   }); 
   $("#popularitySORT").change(function(){
     window.location = "<?php echo site_url();?>/product-category/business-intelligence/?<?php if(isset($_GET['page'])){?>page=<?php echo $_GET['page'];?>&<?php } ?><?php if(isset($_GET['industry'])){?>industry=<?php echo $_GET['industry'];?>&<?php } ?>popularity="+this.value;
   });   
   
   $(".mycustom").click(function(){
   $('#saveBtn').show();
   $('#prodId').val(this.id);
   $('#load').hide(); 
   $('#afterSend').show(); 
   $('#review').val('');
   $('#company').val('');  
   $('#saved').hide();
   $('#failed').hide();
   $('#load').hide();
     //$("#reviewFRM").reset();
   /*   $("#reviewFRM").trigger('reset');
   
     document.getElementById("review").reset();
     document.getElementById("company").reset(); */
   }); 
   
   $( "#reviewFRM" ).on( "submit", function(e) {
   // validate 
   	 $("#reviewFRM").validate({
   			rules: {
   				review: {
   					required: true,
   					minlength: 10
   				},
   				company: {
   					required: true,
   					minlength: 5
   				}
   			},
   			messages: {
   
   				review: {
   					required: "Please enter review",
   					minlength: "Your review must consist of at least 10 characters long"
   				},
   				company: {
   					required: "Please provide company name",
   					minlength: "Your company name must be at least 5 characters"
   				}
   			}
   		}); 
   
   if($('#review').val()==""){return false;}else{$("#errorReview").hide(); }
   if($('#company').val()==""){return false;}else{$("#errorCompany").hide();}
   
   
   
   
   	var dataString = $(this).serializeArray();
   
      $('#load').show(); 
      $('#afterSend').hide(); 
      $('#saveBtn').hide(); 
   
       $.ajax({
   
         type: "POST",
   
         url: "<?php echo site_url();?>/api/api.php?rquest=review",
   
         data: JSON.stringify(dataString),
   
         success: function (data) {
               if(data=="save"){
   				  $('#saved').show();
   				  $('#failed').hide();
   				  $('#load').hide();
   			}else{				 
   				  $('#failed').show();
   				  $('#saved').hide();
                     $('#load').hide();				  
   			}
         }
   
       });
   
       e.preventDefault();
       $('#reviewFRM')[0].reset();
     });
   
   
</script>
</script>
<?php get_footer(); ?>
