<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include('templates/header.php');
?>
<h1> create category Item </h1>

<?php
echo validation_errors();
$this->load->helper('url');


$categoriesName = array();
$categoriesName['-1']="Choose category";
foreach ($categories as $category) {
    $categoriesName[$category['id']] = $category['name'];
}




//echo form_dropdown('shirts', $options, $shirts_on_sale);
echo form_open(base_url().'createSubcategory');
echo '<lable for = "subcategoryId" >Subcategory: </lable>';
echo form_dropdown('categoryId', $categoriesName,$categoriesName['-1'],  'id="categoryId"');
echo '</br>';
?>

<label for="name">Name*</label> 
<input type="input" name="name" /><br />
<label for="descr">Description</label>
<textarea name="descr"></textarea><br />


<input type="submit" name="submit" value="Create Subcategory item" /> 

</form>