<link rel="stylesheet" type="text/css" href=".admin/.css/inventory/inventoryaddbar.css" media="screen" />
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Barcode</h1>
    </div>
</div>
<div class="section group">
  <!-- FORMS -->
  <div id="div1" class="col span_1_of_2">
    <form id="newbarcode" class="formcat">
      <h3>Add a New Barcode</h3>
      <div id="line"></div>
      <br/>
      <b>Barcode:</b></br>
      <div class="bartable">
        <table id="barcode_id" class="display" width="100%" cellspacing="0">
          <thead>
              <tr>
                  <th>Name</th>
                  <th></th>
              </tr>
          </thead>
          <tbody id="barcode-body">
          </tbody>
        </table>
      </div>
    </form>
  <br/>
  </div>

  <div id="div2" class="col span_1_of_2">
    <table id="table_id" class="display" width="100%" cellspacing="0">
      <thead>
          <tr>
              <th>Name</th>
          </tr>
      </thead>
      <tbody id="table-body">
      </tbody>
    </table>
  </div>

</div>
<!-- SCRIPTS HERE!! -->
<script src=".admin/.js\inventory\barcode.js"></script>
<!-- END!! -->

<!--
<input id="name" type="text" required >
<button id="action"></button>
<button id="action"></button>
<button id="action"></button> -->