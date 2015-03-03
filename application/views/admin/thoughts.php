<?php include('templates/header.php'); ?>

<?php include('templates/top_logged_user.php'); ?>

<div class="container theme-showcase container_custom" role="main">
  <div class="page-header">
    <h1>Thoughts</h1>
    <p>Here you can add/delete/modify thoughts.</p>
    <table class="table table-striped">
      <tr>
        <th>Id</th>
        <th>Author</th>
        <th>Text</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Actions</th>
      </tr>
      <tr>
        <td>Id</td>
        <td>Author</td>
        <td>Text</td>
        <td>Start Date</td>
        <td>
          End Date
        <td>
          <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">
            Edit
          </button>
          <button type="button" class="btn btn-default btn-xs">Delete</button>               
        </td>
      </tr>
      <tr>
        <td colspan="6">
          <nav>
            <ul class="pagination pagination-sm pull-right">
              <li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
              <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
            </ul>
          </nav>              
        </td>
      </tr>          
    </table>        
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<?php include('templates/footer.php'); ?> 