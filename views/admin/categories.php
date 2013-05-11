<div class="one_half">

<section class="title">
    <h4><?php echo $title ?></h4>
</section>
  
  <section class="item">
  
    <?php if(count($tree) > 0): ?>
            
        <div  id="page-list" style="padding:10px;">
        <ul class="sortable">

            <?php foreach($tree as $item): ?>
    
                    <li id="page_<?php echo $item['id']; ?>" data-id="<?php echo $item['id']; ?>">
                        <div>
                            <a href="#" rel="<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a>
                        </div>
                
                    <?php if(isset($item['children'])):  ?>
                        <ul>
                            <?php $this->categories_lib->tree_builder($item); ?>
                        </ul>
                    </li>
                
                    <?php else: ?>
                    
                    </li>
                
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>
        </div>
        
    <?php else: ?>

        <?php echo lang('news:error_no_categories'); ?>

    <?php endif; ?>

  </section>
  
</div>
<div class="one_half last" id="page-details">

  <section class="title list">
    <h4><?php echo lang('categories:messages:actions'); ?></h4>
  </section>
  
  <section class="item">  
    <p style="padding:10px;"><?php echo lang('categories:messages:explanations'); ?><br>
        <a href = "admin/categories/create/<?php echo $instance_id; ?>" class="btn blue"><?php echo lang('categories:button:categories:create') ?></a>
    </p> 
    
  </section>
  
</div>


