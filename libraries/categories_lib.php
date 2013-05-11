<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Categories_lib
{        
    // Variables
    protected $ci     = NULL;

    public function __construct()
    {

        // Get CI Instance
        $this->ci =& get_instance();
        $this->ci->load->driver('streams');
        $this->ci->load->helper(array('categories/categories'));
        $this->ci->load->model(array('categories/instances_m'));

    }

    public function create_new_instance_category($name)
    {


        $entry_data = array(
        'title'  => $name,
        );
        $inserted = $this->ci->streams->entries->insert_entry($entry_data, 'instances','categories',  array());
        
        return $inserted;
    }

    public function setup_new_category_field($name = null)
    {
        // get the categories stream
         $stream = $this->ci->db->where('stream_slug','categories')->where('stream_namespace','categories')->get('data_streams')->row();
         $param['id'] =$stream->id;

        // get the instance id

        //if name is empty, create a random one
        if(empty($name)):
            $name = "name not defined _ ".rand_string(7);
        endif;

        $param['instance_id'] = $this->create_new_instance_category($name);

        return $param;
    }

    public function get_instance_name($instance_id)
    {
        $row = $this->ci->instances_m->get($instance_id);
        return $row->title;
    }

    public function get_instance_manager_link($instance_name)
    {
        $this->ci->db->where('title',$instance_name);
        $row = $this->ci->db->get('categories_instances')->row();
        return site_url('admin/categories/instances/manage_categories_for_instance/'.$row->id);
    }

    /**
     * Build the html for a nested list
     *
     * @access public
     * @param array $item
     */
    public function tree_builder($item)
    {
        if (isset($item['children']))
        {
            foreach($item['children'] as $item)
            {
                
                echo '<li id="page_'.$item['id'].'"">';
                
                echo '<div>';
                    echo '<a href="#" rel="'.$item['id'].'">' . $item['title'].'</a>';
                echo '</div>';

                if(isset($item['children']))
                {
                    echo '<ul>';
                        self::tree_builder($item);
                    echo '</ul>';
                    echo '</li>';
                }
                else
                {
                    echo '</li>';
                }
            }
        }
    }

}