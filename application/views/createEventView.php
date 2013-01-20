<?php 
include('templates/header.php');
?>
<h1> create Event Item </h1>

<?php
echo validation_errors();
$this->load->helper('url');


$subcategoriesName = array();
$subcategoriesName['-1'] = 'Choose subcategory';
foreach ($subcategories as $subcategory) {
    $subcategoriesName[$subcategory['id']] = $subcategory['name'];
}




//echo form_dropdown('shirts', $options, $shirts_on_sale);
echo form_open(base_url().'createEvent');
echo '<lable for = "subcategoryId" >Subcategory: </lable>';
echo form_dropdown('subcategoryId',$subcategoriesName, $subcategoriesName['-1'], 'id="subcategoryId"');
echo '</br>';
?>

<label for="name">Name*</label> 
<input type="input" name="name" /><br />
<label for="descr">Description</label>
<textarea name="descr"></textarea><br />
<label for="startDate">Start Date</label> 
<input type="input" name="startDate" /><br />
<label for="endDate">End Date</label> 
<input type="input" name="endDate" /><br />
<label for="fee">Fee</label> 
<input type="input" name="fee" /><br />
<label for="link">Link</label> 
<input type="input" name="link" /><br />

<input type="submit" name="submit" value="Create Subcategory item" /> 

</form>