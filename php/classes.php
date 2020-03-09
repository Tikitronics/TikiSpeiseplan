<?php
class MenuItem {
  // Properties
  public $id;
  public $day;
  public $descr;
  public $add_descr;
  public $side;
  public $price;
  public $restaurant;
  public $restaurant_logo;
  
  // Methods
  function __construct($day, $restaurant, $descr, $price) {
		$this->day = $day;
		$this->restaurant = $restaurant;
		$this->descr = $descr;
		$this->price = $price;
	}
}

class Restaurant {
	// Properties
	public $id;
	public $name;
	public $logoUrl;
	
	// Methods
	function __construct($id, $name, $logo) {
		$this->id = $id;
		$this->name = $name;
		$this->logoUrl = $logo;
  }
}
?>