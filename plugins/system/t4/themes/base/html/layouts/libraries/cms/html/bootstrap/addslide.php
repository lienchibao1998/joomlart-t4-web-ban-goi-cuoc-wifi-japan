<?php 
  $id = $displayData['id'];
  $text = $displayData['text'];
  $class = $displayData['class'];
  $selector = $displayData['selector'];
  $active = $displayData['active'] ? ' show' : '';
  $parent = $displayData['parent'];
  $attrToggle = \T4\Helper\T4Bootstrap::getAttrs(array('toggle'=>'collapse',"target"=>'#'.$id));
  $attrDataParent = \T4\Helper\T4Bootstrap::getAttrs(array("parent"=>'#'.$selector));
?>
  <div class="card">
    <div class="card-header" id="<?php echo $id ?>-header">
      <h2 class="mb-0">
        <button class="btn btn-link" type="button"<?php echo $attrToggle;?>aria-expanded="<?php echo($active ? "true":"false"); ?>" aria-controls="collapseOne">
          <?php echo $text ?>
        </button>
      </h2>
    </div>

    <div id="<?php echo $id ?>" class="collapse<?php echo $active ?>" aria-labelledby="headingOne" <?php echo $attrDataParent;?>>
      <div class="card-body">
