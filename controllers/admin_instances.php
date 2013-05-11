<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin_Instances extends Admin_Controller
{
    protected $section = "instances";
    protected $namespace = "categories";
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('categories');
        $this->load->model('categories_m');
        $this->load->library('categories_lib');
    }

    public function index()
    {

         $extra = 
             array(
             'title'                => lang($this->namespace.':title:'.$this->section.''),
             'buttons' => array(
            array(
                'label'     => lang('global:edit'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/edit/-entry_id-'
            ),
            array(
                'label'     => lang($this->namespace.':button:'.$this->section.':edit_instances_categories'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/manage_categories_for_instance/-entry_id-'
            ),
            array(
                'label'     => lang('global:delete'),
                'url'       => 'admin/'.$this->namespace.'/'.$this->section.'/delete/-entry_id-',
                'confirm'   => true
            )));

         echo   $this->streams->cp->entries_table($this->section, $this->namespace, $pagination = null, $pagination_uri = null, $view_override = true, $extra);
    }

    public function create()
    {

        $extra = array(
            'return'            => 'admin/'.$this->namespace.'/'.$this->section,
            'success_message'   => lang($this->namespace.':messages:'.$this->section.':create:success'),
            'failure_message'   => lang($this->namespace.':messages:'.$this->section.':create:failure'),
            'title'             => lang($this->namespace.':title:'.$this->section.':create')
        );
        
        $this->streams->cp->entry_form($this->section, $this->namespace, 'new', NULL, $view_override = true, $extra);
    }

    public function edit($id)
    {
        $extra = array('title' =>  lang($this->namespace.':title:'.$this->section.':edit'),
        'success_message' => lang($this->namespace.':messages:'.$this->section.':edit:success'),
        'failure_message' => lang($this->namespace.':messages:'.$this->section.':edit:error'),
        'return'          => 'admin/'.$this->namespace.'/'.$this->section   );

        echo $this->streams->cp->entry_form($this->section, $this->namespace, $mode = 'edit', $entry = $id, $view_override = true, $extra, $skips = array());
    }

    public function manage_categories_for_instance($instance_id)
    {

        $this->template
            ->append_css('module::admin.css')
            ->append_js('jquery/jquery.ui.nestedSortable.js')
            ->append_js('jquery/jquery.cooki.js')           
            ->append_js('jquery/jquery.stickyscroll.js');
        $this->template->append_js('module::list.js');

        $data['tree']       = $this->categories_m->get_tree($instance_id);
        $name = $this->categories_lib->get_instance_name($instance_id);
        $data['title']      = str_replace("%n",  $name ,lang('categories:title:manage_categories_for_instance'));
        $data['instance_id'] = $instance_id;

        // Load the view
        $this->template
            ->build('admin/categories', $data);
    }

    public function delete($id)
    {
        //delete affected categories
        $this->categories_m->delete_categories_by_instance($id);
        if($this->streams->entries->delete_entry($id, $this->section, $this->namespace)){
            $this->session->set_flashdata('success', lang($this->namespace.':messages:'.$this->section.':delete:success'));
        }else{
            $this->session->set_flashdata('error', lang($this->namespace.':messages:'.$this->section.':delete:failure'));
        }
        redirect('admin/'.$this->namespace.'/'.$this->section);
    }
}
?>