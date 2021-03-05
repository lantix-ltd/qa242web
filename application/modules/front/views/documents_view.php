<tbody>
        <?php 
            $level="";
            foreach($doc as $keys => $values){ ?>
            
            <tr>
            <td><?php echo $values['doc_name']; ?></td>
            <td>
                <input type="file" name="news_main_page_file_<?php echo $keys; ?>"  data-doc-name="<?php echo $values['doc_name']; ?>" class="file_load">
            </td>
            <td>
                <div class="form-group">
                <label class="control-label col-md-3">Expiry Date</label>
                    <div class='input-group datetimepicker2'>
                    <input type='text' class="form-control" name="expiry_date_<?php echo $keys; ?>" value="<?php echo $values['expiry_date']; ?>"/>
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
                </div>
            </td>
            <td>
            <?php if(!empty($values['document'])){ ?>
                <a href="<?php echo BASE_URL.SUPPLIER_DOCUMENTS_PATH.$values['document'];?>" download><img src="<?php echo STATIC_FRONT_IMAGE.'doc.png' ?>" rel-exist="1"></a>
            <?php }else{ ?>
                <img src="<?php echo STATIC_FRONT_IMAGE.'Delete-file-icon.png' ?>" rel-exist="0">
            <?php } ?>
            </td>
            </tr>
        <?php
        } ?>
    </tbody>
    <script>
            $(document).off('click', '.#submited_form').on('click', '#submited_form', function(e){
                e.preventDefault();
                var valid="1";
                <?php foreach($doc as $key => $value){ ?>
                var vul=$("input[name=news_main_page_file_<?php echo $key?>]").val();
                var exp=$("input[name=expiry_date_<?php echo $key?>]").val();
                if(vul!=="")
                {
                  if(exp=="")
                  {
                      valid=="0";
                      toastr.error("Please Provide Expiration date of the uploaded documents");
                      return false;
                  }
                }
                <?php } ?>
                if(valid="1")
                {
                    $( ".panel-info" ).submit();
                }
            });
    </script>