<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{
    protected $section = "categories";
    protected $namespace = "categories";
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('categories');
        $this->load->model('categories_m');
    }

    public function index()
    {
        redirect('admin/categories/instances');

         $extra = 
             array(
             'title'                => lang($this->namespace.':title:'.$this->section.''),
             'buttons' => array(
            array(
                'label'     => lang('global:edit'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/edit/-entry_id-'
            ),
            array(
                'label'     => lang('global:delete'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/delete/-entry_id-',
                'confirm'   => true
            )));

         echo   $this->streams->cp->entries_table($this->section, $this->namespace, $pagination = null, $pagination_uri = null, $view_override = true, $extra);
    }

    public function create($instance_id)
    {
        $extra = array(
            'return'            => 'admin/categories/instances/manage_categories_for_instance/'.$instance_id,
            'success_message'   => lang($this->namespace.':messages:'.$this->section.':create:success'),
            'failure_message'   => lang($this->namespace.':messages:'.$this->section.':create:failure'),
            'title'             => lang($this->namespace.':title:'.$this->section.':create')
        );

        $default = array('instance_id'  => $instance_id);
        $hidden = array('instance_id');
        
        $this->streams->cp->entry_form($this->section, $this->namespace, 'new', NULL, $view_override = true, $extra, $skips = array(), $tab = false, $hidden, $default);
    }

    public function edit($id)
    {
        $extra = array('title' =>  lang($this->namespace.':title:'.$this->section.':edit'),
        'success_message' => lang($this->namespace.':messages:'.$this->section.':edit:success'),
        'failure_message' => lang($this->namespace.':messages:'.$this->section.':edit:error'),
        'return'          => 'admin/'.$this->namespace.'/'.$this->section   );

        echo $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'edit', $entry = $id, $view_override = true, $extra, $skips = array());
    }


    public function delete($id)
    {
        if($this->streams->entries->delete_entry($id, $this->section, $this->namespace)){
            $this->session->set_flashdata('success', lang($this->namespace.':messages:'.$this->section.':delete:success'));
        }else{
            $this->session->set_flashdata('error', lang($this->namespace.':messages:'.$this->section.':delete:failure'));
        }
        redirect('admin/'.$this->namespace.'/'.$this->section);
    }

    /**
     * Order the items and record their children
     *
     * @access public
     * @return string json message
     */
    public function order()
    {
        $order      = $this->input->post('order');
        $data       = $this->input->post('data');
        $root_items = isset($data['root_items']) ? $data['root_items'] : array();

        if (is_array($order))
        {
            //reset all parent > child relations
            $this->categories_m->update_all(array('parent_id' => NULL));

            foreach ($order as $i => $list)
            {
                //set the order of the root lists
                $this->categories_m->update_by('id', $list['id'], array('ordering_count' => $i));

                //iterate through children and set their order and parent
                $this->categories_m->_set_children($list);
            }
        }
    }


    /**
     * Show details
     *
     * @access  public
     * @param   $id
     * @return  void
     */
    public function ajax_load($id)
    {
        // Load the entry
        $data['entry'] = $this->streams->entries->get_entry($id, 'categories', 'news', true);

        // Get the limb
        $limb = $this->categories_m->get_tree($id);

        // Get IDs
        $ids = $this->categories_m->get_ids($limb);

        // Get all non-disabled
        $data['entry']->subscriptions_count = $this->db->distinct()->select('subscribers_id')->where_in('lists_id', $ids)->where('disabled', 0)->get('newsletters_subscribers_lists')->num_rows();

        // Get all disabled
        $data['entry']->disabled_count = $this->db->distinct()->select('subscribers_id')->where_in('lists_id', $ids)->where('disabled', 1)->get('newsletters_subscribers_lists')->num_rows();
        
        // Load the view
        $this->load->view('admin/ajax/category_detail', $data);
    }
}
?>