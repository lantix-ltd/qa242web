<div class="page-content-wrapper">
<?php // print_r($post['title']);exit; ?>
        <!-- END PAGE HEADER-->
       
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet box blue">
                    <div class="portlet-title">

                       
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                       <legend> Supplier</legend>
                            <div class="form-body">
                                     <table id="datatable1" class="table table-bordered">
                                        <tbody class="table-body">
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  Name:
                                              </th>
                                              <td>
                                                  <?php echo $post['name']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  Email:
                                              </th>
                                              <td>
                                                  <?php echo $post['email']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  Phone #:
                                              </th>
                                              <td>
                                                  <?php echo $post['phone_no']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                Address:
                                              </th>
                                              <td>
                                                  <?php echo $post['address']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  City:
                                              </th>
                                              <td>
                                                  <?php echo $post['city']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  Country:
                                              </th>
                                              <td>
                                                  <?php echo $post['country']; ?>
                                              </td>
                                          </tr>
                                          <tr class="bg-col">
                                              <th style="color: #6c9cde!important;">
                                                  State:
                                              </th>
                                              <td>
                                                  <?php echo $post['state']; ?>
                                              </td>
                                          </tr>
                                      </tbody>
                                    </table>
                                </div>
                    				<legend> Ingredients List </legend>
                           			 <div class="form-body">   
                                    <table class="table table-bordered" style="color: black !important">
                                    <thead>
                                      <tr>
                                        <th  style="color: #6C9CDE !important">NAV Number</th>
                                        <th style="color: #6C9CDE !important">RM PLM Number Name</th>
                                        <th style="color: #6C9CDE !important">Raw Material Name</th>
                                      	<th style="color: #6C9CDE !important">Supplier Status</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                         <?php if(isset($ingredients) && !empty($ingredients)){
                                        foreach($ingredients as $value){?>
                                      <tr>
                                        <td><?php echo $value['item_no']; ?></td>
                                        <td> <?php echo $value['plm_no']; ?></td>
                                        <td> <?php echo $value['item_name']; ?></td>
                                      	<td> <?php echo $value['role']; ?></td>
                                      </tr>
                                      <?}}else{ ?>
                                    	 <tr>
                                        <td></td>
                                        <td>No Result Found</td>
                                        <td> </td>
                                      	<td> </td>
                                      </tr>
                                     <?php } ?>
                                    </tbody>
                                  </table>
                            	</div>
                               <legend> Documents</legend>
                                    <div class="form-body">
                                             <table id="datatable1" class="table table-bordered">
                                                <tbody class="table-body">
                                                    <?php
                                                    if(!empty($doc)){
                                                    foreach($doc as $key => $value){ ?>
                                                  <tr class="bg-col">
                                                      <th style="color: #6c9cde!important;">
                                                          <?php echo $value['doc_name']; ?>:
                                                      </th>
                                                      <td <?php if($value['expiry_date']<= date('Y-m-d') ){?> style="color:red;"<?php }?>>
                                                          <?php echo date("m-d-Y", strtotime($value['expiry_date'])); ?> 
                                                      </td>
                                                      <td>
                                                          <a href="<?php echo BASE_URL.SUPPLIER_DOCUMENTS_PATH.$value['document'];?>" download <?php if($value['expiry_date']<= date('Y-m-d') ){?> style="color:red;"<?php }?>><?php echo $value['document']; ?></a>
                                                      </td>
                                                  </tr>
                                                  <?php }
                                                  }else {
                                                  echo "No documents Uploaded";
                                                  } ?>
                                            </tbody>
                                        </table>
                                    </div>
                        <!-- END FORM-->
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
<!--    </div>-->
</div>
</div>


