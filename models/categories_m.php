<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author        	Ryan Thompson - AI Web Systems, Inc.
 * @website       	http://aiwebsystems.com
 * @package 		CMS
 */
class Categories_m extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		
		$this->_table = 'categories_categories';
	}

	public function delete_categories_by_instance($instance_id)
	{
		$this->db->where('instance_id',$instance_id);
		$this->db->delete($this->_table);

	}

	/*-----------------------------------------------------------------------------------------*/

	/**
	 * Build a multi-array of parent > children.
	 *
	 * @author AI Web Systems, Inc. - Ryan Thompson
	 * @access public
	 * @return array
	 */
	public function get_tree($instance_id, $id = false)
	{
		$all_items = $this->db
			 ->order_by('parent_id')
			 ->order_by('ordering_count')
			 ->where('instance_id', $instance_id)
			 ->get($this->_table)
			 ->result_array();
		
		$items = array();
		$item_array = array();
		
		// we must reindex the array first
		foreach ($all_items as $item)
		{
			$items[$item['id']] = $item;
		}
		
		unset($all_items);
		
		// build a multidimensional array of parent > children
		foreach ($items as $item)
		{			
			if (array_key_exists($item['parent_id'], $items))
			{				
				// add this list to the children array of the parent list
				$items[$item['parent_id']]['children'][] =& $items[$item['id']];
			}
			
			// Are we looking for a particular tree?
			if ( ! $id )
			{
				// Nope, root is root
				if ($item['parent_id'] == 0 || is_null($item['parent_id']))
				{
					$item_array[] =& $items[$item['id']];
				}
			}
			else
			{
				// Yep, $id is root
				if ($item['id'] == $id)
				{
					$item_array =& $items[$item['id']];
				}
			}
		}
		return $item_array;
	}

	/*-----------------------------------------------------------------------------------------*/

	/**
	 * Set the parent > child relations and child order
	 *
	 * @author 	Ryan Thompson - AI Web Systems, Inc.
	 * @param 	array $item
	 * @return 	void
	 */
	public function _set_children($item)
	{
		if (isset($item['children']))
		{
			foreach ($item['children'] as $i => $child)
			{
				$this->db->where('id', $child['id']);
				$this->db->update($this->_table, array('parent_id' => $item['id'], 'ordering_count' => $i));
				
				//repeat as long as there are children
				if (isset($child['children']))
				{
					$this->_set_children($child);
				}
			}
		}
	}

	/*-----------------------------------------------------------------------------------------*/
	
	/**
	 * Get IDs of lists in multi-array of parent > children
	 *
	 * @author AI Web Systems, Inc. - Ryan Thompson
	 * @access public
	 * @param array $lists
	 * @return array An array representing the IDs found
	 */
	public function get_ids($items, $ids = array())
	{
		// The current ID
		(!in_array($items['id'], $ids)?$ids[] = $items['id']:NULL);
		

		// Are there children?
		if(isset($items['children']))
		{
			foreach($items['children'] as $k => &$item)
			{
				// If this iteration has children then walk again
				if(isset($item['children']))
				{
					// Add each result to the array
					foreach($this->categories_m->get_ids($item) as $id)
					{
						(!in_array($id, $ids)?$ids[] = $id:NULL);
					}
				}
				else
				{
					// Just add this one - no children
					(!in_array($item['id'], $ids)?$ids[] = $item['id']:NULL);
				}
			}
		}
		
		return $ids;
	}

	/*-----------------------------------------------------------------------------------------*/
	
	/**
	 * Get IDs of lists in a list by its ID
	 *
	 * @author AI Web Systems, Inc. - Ryan Thompson
	 * @access public
	 * @param array $id
	 */
	public function get_ids_in_list($id)
	{
		$items = $this->get_tree($id);

		return $this->get_ids($items);
	}
}
