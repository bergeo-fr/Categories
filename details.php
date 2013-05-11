<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Categories extends Module {

    public $version = 0.2;
	public $module_name = 'categories';

	public function info()
	{
		return array (
  'name' => 
  array (
    'en' => 'Categories Manager',
  ),
  'description' => 
  array (
    'en' => 'An awesome categories manager for all of your modules in PyroCMS',
  ),
  'backend' => true,
  'plugin' => true,
  'events' => true,
  'menu' => 'content', 
  'sections'  => array(
               /* 'categories' => array(
                    'name'  => 'categories:title:categories',
                    'uri'   => 'admin/categories',
                    'shortcuts' => array(
                        'categories:create' => array(
                            'name'  => 'categories:button:categories:create',
                            'uri'   => 'admin/categories/create',
                            'class' => ''
                        )
                    )
                ),*/
                'instances' => array(
                    'name'  => 'categories:title:instances',
                    'uri'   => 'admin/categories/instances',
                    'shortcuts' => array(
                        'instances:create' => array(
                            'name'  => 'categories:button:instances:create',
                            'uri'   => 'admin/categories/instances/create',
                            'class' => ''
                        )
                    )
                ),
));
	}

	public function install()
	{
        $stream_slug = "instances";

        //Add the stream
        $this->streams->streams->add_stream('lang:'. $this->module_name.':title:'.$stream_slug, $stream_slug, $this->module_name, $this->module_name.'_', null);

        //Load some info for later
        $streams[$stream_slug] = $this->streams->streams->get_stream($stream_slug,  $this->module_name);
        
        $field_slug = "title";
        if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
        {
            $field = array(
                'name'              => 'lang:'.$this->module_name.':fields:'.$field_slug,
                'slug'              => $field_slug,
                'namespace'         => $this->module_name,
                'type'              => 'text',
                'extra'             => array(
                    'max_length'        => 255
                ),
                'assign'            => $stream_slug,
                'title_column'      => true,
                'required'          => true,
                'unique'            => false,
                'instructions' =>  'lang:'.$this->module_name.':instructions:'.$field_slug.'_instance'
            );
            $this->streams->fields->add_field($field);
        }
        //Update view options
        $update_data = array(
            'view_options'=> array('id','title')
        );

        $this->streams->streams->update_stream($stream_slug, $this->module_name, $update_data);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Add Streams - Categories
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $stream_slug = "categories";

        //Add the stream
        $this->streams->streams->add_stream('lang:'. $this->module_name.':title:'.$stream_slug, $stream_slug, $this->module_name, $this->module_name.'_', null);

        //Load some info for later
        $streams[$stream_slug] = $this->streams->streams->get_stream($stream_slug,  $this->module_name);

       //Update view options
        $update_data = array(
            'view_options'=> array('title')
        );

        $this->streams->streams->update_stream($stream_slug, $this->module_name, $update_data);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Add Fields Categories - the defaults one
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $field_slug = "title";
        $this->streams->fields->assign_field($this->module_name, $stream_slug, $field_slug , 
            array('required' => true, 'title_column' => true, 'unique' => false, 'instructions' => 'lang:'.$this->module_name.':instructions:'.$field_slug));

        $field_slug = "slug";
        if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
        {
            $field = array(
                'name'              => 'lang:'.$this->module_name.':fields:'.$field_slug,
                'slug'              => $field_slug,
                'namespace'         => $this->module_name,
                'type'              => 'slug',
                'extra'             => array(
                    'space_type'        => "-",
                    'slug_field'                  => "title"
                ),
                'assign'            => $stream_slug,
                'title_column'      => false,
                'required'          => true,
                'unique'            => false
            );
            $this->streams->fields->add_field($field);
        }

        $field_slug = "parent_id";
        if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
        {
            $field = array(
                'name'              => 'lang:'.$this->module_name.':fields:'.$field_slug,
                'slug'              => $field_slug,
                'namespace'         => $this->module_name,
                'type'              => 'relationship',                
                'extra'             =>array(
                    'choose_stream' =>$streams['categories']->id
                    ),
                'assign'            => $stream_slug,
                'title_column'      => false,
                'required'          => false,
                'unique'            => false
            );
            $this->streams->fields->add_field($field);
        }
        
        $field_slug = "instance_id";
        if($this->db->where('field_namespace', $this->module_name)->where('field_slug', $field_slug)->limit(1)->get('data_fields')->num_rows()==null)
        {
            $field = array(
                'name'              => 'lang:'.$this->module_name.':fields:'.$field_slug,
                'slug'              => $field_slug,
                'namespace'         => $this->module_name,
                'type'              => 'relationship',
                'extra'             =>array(
                    'choose_stream' =>$streams['instances']->id
                    ),
                'assign'            => $stream_slug,
                'title_column'      => false,
                'required'          => false,
                'unique'            => false,
                'instructions' =>  'lang:'.$this->module_name.':instructions:'.$field_slug
            );
            $this->streams->fields->add_field($field);
        }
 
        return true;
	}

	public function uninstall()
	{
        $this->streams->utilities->remove_namespace($this->module_name);
		return true;
	}

	public function upgrade($old_version)
	{
                //Update view options
        $update_data = array(
            'view_options'=> array('id','title')
        );

        $this->streams->streams->update_stream('instances', "categories", $update_data);

		return TRUE;
	}

	public function help()
	{
	}
}
/* End of file details.php */
?>