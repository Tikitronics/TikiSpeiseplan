<?php
class MenuItem {
  // Properties
  public $day;
  public $descr;
  public $add_descr;
  public $price;
  public $foodType;
  public $pictureUrl;
  public $restaurant;
  
  // Methods
  function __construct($day, $restaurant, $descr, $price, $add_descr, $foodType, $pictureUrl) {
		$this->day = $day;
		$this->restaurant = $restaurant;
		$this->descr = $descr;
		$this->price = $price;
		$this->add_descr = $add_descr;
		$this->food_type = $foodType;
		$this->pictureUrl = $pictureUrl;
	}
}

class Restaurant {
	// Properties
	public $name;
	public $logoUrl;
	
	// Methods
	function __construct($name, $logo) {
		$this->name = $name;
		$this->logoUrl = $logo;
  }
}
?>