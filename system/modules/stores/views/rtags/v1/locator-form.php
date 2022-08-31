<div id="cb-store-locator">
   <div id="cb-store-locator-start">
      <div id="cb-store-locator-start-inner">

<form method="post" action="<?=$action;?>" class="horizontal">
   <fieldset>

      <div class="field">
         <label for="item">Item:</label>
         <?=form_dropdown('item', $items, $this->validation->item, 'class = item');?>
         <?=$this->validation->item_error;?>
      </div>

      <div class="field">
         <label for="zip">Zip:</label>
         <?=form_input(array('class'=>'text', 'name'=>'zip', 'id'=>'zip', 'maxlength'=>'5', 'size'=>'15', 'value'=>$this->validation->zip));?>
         <?=$this->validation->zip_error;?>
      </div>

      <div class="field">
         <label for="radius">Distance:</label>
         <?=form_dropdown('radius', $radii, $this->validation->radius);?>
         <?=$this->validation->radius_error;?>
      </div>

      <div class="field">
         <input type="hidden" name="count" value="50">
      </div>
      <div class="field">
         <input type="hidden" name="brand" value="<?=$brand;?>">
      </div>
      <div class="field">
         <input type="hidden" name="sort" value="Distance">
      </div>

      <div class="buttons">
         <input type="submit" class="button" value="Find Stores">
      </div>
   </fieldset>
</form>

      </div>   <?php // cb-store-locator-start-inner ?>
   </div>   <?php // cb-store-locator-start ?>
</div>   <?php // cb-store-locator ?>