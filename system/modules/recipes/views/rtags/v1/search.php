<div id="recipe-search" class="clearfix">
   <div id="recipe-search-inner" class="clearfix">

   <div id="recipe-search-results">
      <div id="recipe-search-results-inner">

<?php if (isset($recipes)): ?>
   <?php if ( ! empty($recipes)): ?>

<div id="recipe-list" class="clearfix">

<h2>Search Results
<br /><span><?=$search_desc;?></span></h2>

   <?php $count = 0; ?>

   <?php foreach ($recipes AS $recipe): ?>

   <?php $count++; ?>
   
   <?php $my_detail_url = str_replace('{RecipeCode}', $recipe['RecipeCode'], $detail_url); ?>
   
   <?php if ($count == 1): ?>
   <ul>
   <?php endif; ?>
   
   <li><a href="<?=$my_detail_url;?>"><?=$recipe['Title'];?></a><?php if ($recipe['Featured'] == 1): ?> <span class="featured">Featured</span><?php endif; ?><?php if ($recipe['FlagAsNew'] == 1): ?> <span class="new">New!</span><?php endif; ?></li>
   
   <?php if ($count == count($recipes)): ?>
   </ul>
   <?php endif; ?>

   <?php endforeach; ?>
	
</div>

   <?php else: ?>

<div id="recipe-list-no-results" class="clearfix">

   <h2>No results found.</h2>
   <p>Try widening your search by entering fewer search words or setting the product and category pulldowns to "All".</p>
   
</div>  <?php /* recipe-list-no-results */ ?>

   <?php endif; ?>

<?php else: ?>

   <?php if ($home_page == ''): ?>

<div id="recipe-list-default-home" class="clearfix">

<p class="instruct">Enter a search term in the field on the right, select a product or category, or do both to narrow your search as much as you want.</p>

   <div class="manual">

<p>This is the default recipe home page before a search is made.</p>

<p>To override this content, create a file containing the HTML you want to display and point the <kbd>home-page</kbd> variable to it in the rTag config:</p>

<pre>
$config['home-page'] = 'http://example.com/recipes/home.html';
</pre>

<p>The URL needs to be fully-qualified as it will be referenced from the rTag server. Any <kbd>&lt;img&gt;</kbd> tags will also need to have fully-qualified URLs.</p>

<p>The content of the HTML file should be an HTML snippet, and NOT a complete HTML page with <kbd>&lt;html&gt;</kbd>, <kbd>&lt;head&gt;</kbd>, or <kbd>&lt;body&gt;</kbd> tags. The content is inserted into the template directly and not through a iFrame.</p>

   </div>  <?php /* manual */ ?>
</div>  <?php /* recipe-list-default-home */ ?>

   <?php else: ?>
   
<div id="recipe-list-custom-home" class="clearfix">
      <?=$home_page;?>
</div>
   
   <?php endif; ?>

<?php endif; ?>

      </div>  <?php /* recipe-search-results-inner */ ?>
   </div>  <?php /* recipe-search-results */ ?>

   <div id="recipe-search-form">
      <div id="recipe-search-form-inner">
      
<form method="post" action="<?=$action;?>" name="recipe" id="recipe">

   <h2>Search Recipes:</h2>

<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

   <p>Narrow your results by selecting categories below.</p>

   <label for="Product">Products:</label>
<?=form_dropdown('Product', $products, $this->validation->Product);?>
<?=$this->validation->Product_error;?>

   <?php foreach ($lists AS $item): ?>
      <?php $error_name = $item['Code'].'_error'; ?>

   <label for="<?=$item['Code'];?>"><?=$item['Name'];?>:</label>
<?=form_dropdown($item['Code'], $item['List'], $this->validation->$item['Code']);?>
<?=$this->validation->$error_name;?>

   <?php endforeach; ?>
   
   <input type="submit" name="rcpSearch" id="rcpSearch" value="Search" style="display:inline;" /> <span class="return-link">or <a href="<?=$action;?>">Go to Recipe Home</a></span>

</form>

      </div>  <?php /* recipe-search-form-inner */ ?>
   </div>  <?php /* recipe-search-form */ ?>

   </div>  <?php /* recipe-search-inner */ ?>
</div>  <?php /* recipe-search */ ?>