<?php
// file: model/MonthSpending.php

/**
* Class MonthSpending
*
* Represents a MonthSpending in the Application. It is defined with the type,
* month, year and sum of the spendings that accomplish the condition of being created
* among some certain dates
*
*
* @author Juan Yuri Díaz Sánchez
*/
class MonthSpending{

	/**
	* The type of this spending
	* @var string
	*/
	private $type_spending;

	/**
	* The month when the spending was created
	* @var integer
	*/
	private $month;

	/**
	* The year when the spending was created
	* @var integer
	*/
	private $year;

    /**
    * The sum of all spendings of the months
    * @var float
    */
    private $sum;

    /**
	* The constructor
	*
	* @param string $type_spending The type of the spending
	* @param integer $month The month of the spending
	* @param integer $year The year of the spending
    * @param float $sum The sum of all spendings of a month
	*/
    public function __construct($type_spending=NULL, $month=NULL, $year=NULL, $sum=NULL) {
        $this->type_spending = $type_spending;
        $this->month = $month;
        $this->year = $year;
        $this->sum = $sum;
    }


    /**
	* Gets the type of this spending
	*
	* @return string The type of this spending
	*/
	public function getType() {
		return $this->type_spending;
	}

	/**
	* Sets the type of this spending
	*
	* @param string $type the type of this spending
	* @return void
	*/
	public function setType($type_spending) {
		$this->type_spending = $type_spending;
	}

    /**
	* Gets the month of this spending
	*
	* @return integer The month of this spending
	*/
	public function getMonth() {
		return $this->month;
	}

	/**
	* Sets the month of this spending
	*
	* @param integer $month the month of this spending
	* @return void
	*/
	public function setMonth($month) {
		$this->month = $month;
	}

    /**
	* Gets the year of this spending
	*
	* @return integer The year of this spending
	*/
	public function getYear() {
		return $this->year;
	}

	/**
	* Sets the year of this spending
	*
	* @param integer $year the year of this spending
	* @return void
	*/
	public function setYear($year) {
		$this->year = $year;
	}

    /**
	* Gets the sum of this spending
	*
	* @return float The sum of this spending
	*/
	public function getSum() {
		return $this->sum;
	}

	/**
	* Sets the sum of this spending
	*
	* @param float $sum the sum of this spending
	* @return void
	*/
	public function setSum($sum) {
		$this->sum = $sum;
	}
}

