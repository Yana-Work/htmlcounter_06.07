<?php include 'header.php';?>
<div class="wrapper">
   <section class="form">
      <h2>HTML Element Counter</h2>
      <div id="errContainer"></div>
      <form action="index.php?pagge=result" method="post" >
         <div class="field url">
            <label>URL</label>
            <input type="text" name="url" placeholder="http://example.com/en" required>
         </div>
         <div class="field element">
            <label>Element</label>
            <input type="text" name="element" placeholder="<img>" required>
         </div>
         <div class="field button">
            <button type="submit" name="send">Submit</button>
         </div>
      </form>
   </section>
</div>
<?php include 'footer.php';?>

