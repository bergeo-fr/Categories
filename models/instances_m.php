<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author          Ryan Thompson - AI Web Systems, Inc.
 * @website         http://aiwebsystems.com
 * @package         CMS
 */
class Instances_m extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'categories_instances';
        
    }

}
