<?php

// This function is used to render the pagination elements.
//
function kriesi_pagination($cur_page=0, $tot_pages=1, $range = 2)
{ 
	$tab = $_REQUEST['tab'];
    $showitems = ($range * 2)+1;  

     if(1 != $tot_pages)
     {
         echo "<div class='pagination'>";
         if($cur_page > 2 && $cur_page > $range+1 && $showitems < $tot_pages) echo "<a href='?page=" . DLSP_ADMIN_PAGE . "&tab=" . $tab . "&p=0'>&laquo;</a>";
         if($cur_page > 1 && $showitems < $tot_pages) echo "<a href='?page=" . DLSP_ADMIN_PAGE . "&tab=" . $tab . "&p=" . ($cur_page-1) . "'>&lsaquo;</a>";

         for ($i=0; $i < $tot_pages; $i++)
         {
             if (0 != $tot_pages &&( !($i >= $cur_page+$range+1 || $i <= $cur_page-$range-1) || $tot_pages <= $showitems ))
             {
                 echo ($cur_page == $i)? "<span class='current'>".($i+1)."</span>":"<a href='?page=" . DLSP_ADMIN_PAGE . "&tab=" . $tab . "&p=" . $i . "' class='inactive' >".($i+1)."</a>";
             }
         }

         if ($cur_page < $tot_pages-1 && $showitems < $tot_pages) echo "<a href='?page=" . DLSP_ADMIN_PAGE . "&tab=" . $tab . "&p=" . ($cur_page+1) . "'>&rsaquo;</a>";  
         if ($cur_page < $tot_pages-2 &&  $cur_page+$range-1 < $tot_pages && $showitems < $tot_pages) echo "<a href='?page=" . DLSP_ADMIN_PAGE . "&tab=" . $tab . "&p=".($tot_pages-1)."'>&raquo;</a>";
         echo "</div>\n";
     }
}

?>