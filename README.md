Categories
==========

Categories Manager for PyroCMS

#Installation

##Required
Download and install the nested field relationship.

##Installation 
Download the package and install it in your PyroCMS instance.


#How to use it 

##How to add a category field in a Stream ?
Just theses few lines of code in your detail.php

    //Load the library 
    $this->load->library('categories/categories_lib');
    //get some data
    $setup = $this->categories_lib->setup_new_category_field("blog_test_cat");
    
    //now create your category field for your stream
     $field = array(
                'name'              => 'lang:namespace:fields:field_slug',
                'slug'              => 'field_slug',
                'namespace'         => 'namespace',
                'type'              => 'categories_nested_list_relationship',
                'extra'             => array(
                    'nested_list_stream' => $setup['id'],
                    'instance_id' => $setup['instance_id'], 
                    'allow_disabled' => 0),
                
                'assign'            => 'stream_slug',
                'title_column'      => true,
                'required'          => true,
                'unique'            => false
            );
            $this->streams->fields->add_field($field);

##How to add a category menu in my module ?

You need to load the library in info() method in the detail.php
    $this->load->library('categories/categories_lib');
and get the url of your instance by calling this method : (NB the name in the parameter is the name used when you create the field

     'sections'  => array(
                'categories' => array(
                    'name'  => 'Categories',
                    'uri'   => $this->categories_lib->get_instance_manager_link('blog_test_cat'),
                ),


