

<?php include('templates/header.php'); ?>
    
    <h1>Your form was successfully submitted!</h1>
    <h1>new <?php echo $formName ?> was created</h>
    <p><?php echo anchor('createCategory', 'Create New category'); ?></p>
    <p><?php echo anchor('createSubcategory', 'Create New subcategory'); ?></p>
    <p><?php echo anchor('createEvent', 'Create New event'); ?></p>
    
    
<?php include('templates/footer.php'); ?>
