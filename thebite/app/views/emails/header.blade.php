<tr>
    <td valign="top">
        <!-- Begin Header -->
        <table width="100%" style="  border-bottom: 1px solid #EEEEEE; text-align: left; padding-top: 10px; ">
            <!--#F76F24-->
            <tr>
                <td><a style="margin-left:0px" href="<?php echo HTTP_PATH; ?>">
                        
                         <?php
                            if(file_exists(UPLOAD_LOGO_IMAGE_PATH.SITE_LOGO)){
                                 ?>
                                   {{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array('alt'=>SITE_TITLE,'title'=>SITE_TITLE)) }}

                               <?php 
                            }else{
                                ?>
                                 <img src="{{ URL::asset('public/img/front') }}/logo.png" alt="<?php echo SITE_TITLE; ?>" title="<?php echo SITE_TITLE; ?>" />

                               <?php
                            }
                           ?>
                    </a>
                </td>
            </tr>
        </table>
        <!-- End Header -->
    </td>
</tr>