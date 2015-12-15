<?php

/**
 * Category class
 * @author H. Eilers
 *
 */
class Category{
	
	public $id = 0;
	public $parent = null;
	public $warehouseId = 0;
	public $name = null;
	public $demand = 0;
	public $countInCartons = false;
	
	public $children = array();
	/**
	 * Constructor
	 * @param integer $id			Category ID, null to create new one, -1 for doeing nothing.
	 * @param integer $warehouseId	Warehouse ID, if to create new category.
	 * @param boolean $update		Set true to receive category information
	 * 								from database, instead of from cache. 
	 */
	public function __construct($id=null, $warehouseId=0, $update=false){
		if( $id == null )
			$this->create($warehouseId);
		elseif( is_integer($id) && $id > 0 )
			$this->updateFromDatabase($id, $warehouseId, $update);
	}
	
	/**
	 * Update category entry in database
	 */
	public function edit(){
		if( $this->id > 0 && $this->warehouseId > 0
				&& is_string($this->name) && strlen($this->name) > 0 ){
			$sql = "UPDATE ".Database::getTableName('categories')." SET parent=?, warehouse=?, name=?, demand=?, countInCartons=? WHERE id=?";
			$response = Database::getInstance()->sql( 'editCategory', $sql, 'iisiii', [
					$this->parent,
					$this->warehouseId,
					$this->name,
					$this->demand,
					$this->countInCartons,
					$this->id
			], false );
			
			if( is_array($response) ){
				Log::debug( 'Edited category #'.$this->id );
				return true;
			}
		} else {
			Log::warn( 'Category id is #'.$this->id );
		}
		
		return false;
	}
	
	/**
	 * Deletes category
	 */
	public function delete(){
		$sql = "DELETE FROM ".Database::getTableName('categories')." WHERE warehouse=? AND id=?";
		return Database::getInstance()->sql( 'deleteCategory', $sql, 'ii', [$this->warehouseId, $this->id], false );
	}
	
	/**
	 * Creates a new category
	 * @param integer $warehouseId	Warehouse ID
	 */
	private function create($warehouseId){
		$sql = "INSERT INTO ".Database::getTableName('categories')." (warehouse) VALUES(?)";
		$id = Database::getInstance()->sql( 'insertCategory', $sql, 'i', [$warehouseId] );
		if( is_integer($id) ){
			$this->id = $id;
			$this->warehouseId = $warehouseId;
			Log::debug( 'Created new category #'.$id );
		}
	}
	
	/**
	 * Receive category from database.
	 * @param integer $id			Category ID
	 * @param integer $warehouseId	Warehouse ID
	 * @param integer $update		Set true to update from database, insteadt from cache.
	 */
	private function updateFromDatabase($id, $warehouseId, $update){
		$sql = "SELECT * FROM ".Database::getTableName('categories')." WHERE warehouse=? AND id=?";
		$response = Database::getInstance()->sql( 'getCategory', $sql, 'ii', [$warehouseId, $id], !$update );
		if( $response && count($response) > 0 ){
			$response = $response[0];
			$this->id = $response['id'];
			$this->parent = $response['parent'];
			$this->warehouseId = $response['warehouse'];
			$this->name = $response['name'];
			$this->demand = $response['demand'];
			$this->countInCartons = $response['countInCartons'];
		}
	}
	
	/**
	 * Gets categories as hierarchy array.
	 * @param integer $warehouseId	Warehouse ID
	 */
	public static function getCategories($warehouseId, $parentId=null){
		$categories = [];
		
		if( $parentId == null ){
			$sql = "SELECT * FROM ".Database::getTableName('categories')." WHERE warehouse=? AND parent IS NULL ORDER BY name ASC";
			$response = Database::getInstance()->sql( 'getCategories', $sql, 'i', [$warehouseId], false );
		} elseif( $parentId < 0 ) {
			$sql = "SELECT * FROM ".Database::getTableName('categories')." WHERE warehouse=? ORDER BY name ASC";
			$response = Database::getInstance()->sql( 'getCategories', $sql, 'i', [$warehouseId], false );
		} else {
			$sql = "SELECT * FROM ".Database::getTableName('categories')." WHERE warehouse=? AND parent=? ORDER BY name ASC";
			$response = Database::getInstance()->sql( 'getCategories', $sql, 'ii', [$warehouseId, $parentId], false );
		}
		
		// add root categories
		if( is_array($response) ){			
			foreach( $response as $entry ){				
				// add category to list
				$category = new Category(-1);
				$category->id = $entry['id'];
				$category->parent = $entry['parent'];
				$category->warehouseId = $entry['warehouse'];
				$category->name = $entry['name'];
				$category->demand = $entry['demand'];
				$category->countInCartons = $entry['countInCartons'];
				array_push( $categories, $category );
			}
		}
		
		return $categories;		
	}
	
}

?>	