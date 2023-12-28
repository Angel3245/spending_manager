<?php
// file: model/SpendingMapper.php
require_once(__DIR__ . "/../core/PDOConnection.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Spending.php");
require_once(__DIR__ . "/../model/MonthSpending.php");

/**
 * Class SpendingMapper
 *
 * Database interface for Spending entities
 *
 * @author Juan Yuri Díaz Sánchez
 */
class SpendingMapper
{

	/**
	 * Reference to the PDO connection
	 * @var PDO
	 */
	private $db;

	public function __construct()
	{
		$this->db = PDOConnection::getInstance();
	}

	/**
	 * Retrieves all spendings
	 *
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of Spending instances 
	 */
	public function findAll()
	{

		$queryFindAll = "SELECT * FROM spending, users WHERE users.alias = spending.owner_spending";
		/* $stmt->execute( array( $currentusername */
		$stmt = $this->db->query($queryFindAll);
		$spending_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$spendings = array();

		foreach ($spending_db as $spending) {
			$owner = new User($spending["owner_spending"]);
			array_push($spendings, new Spending(
				$spending["id_spending"],
				$spending["type_spending"],
				$spending["date_spending"],
				$spending["qty_spending"],
				$spending["description_spending"],
				$spending["doc_name_spending"],
				$spending["file_name_on_server"],
				$owner
			));
		}

		return $spendings;
	}


	/**
	 * Retrieves all spendings of a user
	 * @param string $alias The alias of the current user
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of Spending instances 
	 */
	public function findAllMine($alias)
	{
		$queryFindAllMine = "SELECT * FROM spending, users WHERE users.alias=spending.owner_spending and users.alias=? ORDER BY date_spending";
		$stmt = $this->db->prepare($queryFindAllMine);
		$stmt->execute(array($alias));
		$spending_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$spendings = array();

		foreach ($spending_db as $spending) {
			$owner = new User($spending["owner_spending"]);
			array_push($spendings, new Spending(
				$spending["id_spending"],
				$spending["type_spending"],
				$spending["date_spending"],
				$spending["qty_spending"],
				$spending["description_spending"],
				$spending["doc_name_spending"],
				$spending["file_name_on_server"],
				$owner
			));
		}

		return $spendings;
	}


	/**
	 * Retrieves all spendings of a user
	 * @param string $alias The alias of the current user
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of Spending instances 
	 */
	public function findAllDocServer($alias)
	{
		$queryFindAllMine = "SELECT file_name_on_server FROM spending, users WHERE users.alias=spending.owner_spending and users.alias=? and file_name_on_server IS NOT NULL";
		$stmt = $this->db->prepare($queryFindAllMine);
		$stmt->execute(array($alias));
		$spending_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$docs = array();

		foreach ($spending_db as $doc) {
			array_push($docs, $doc);
		}

		return $docs;
	}


	/**
	 * Loads a Spending from the database given its id
	 *
	 * @param Spending $id_spending The specific spending to find
	 * @throws PDOException if a database error occurs
	 * @return Spending The Spending instances.
	 * NULL if the Spending is not found
	 */
	public function findById($id_spending)
	{
		$queryFindById = "SELECT * FROM Spending WHERE id_spending=?";
		$stmt = $this->db->prepare($queryFindById);
		$stmt->execute(array($id_spending));
		$spending = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($spending != null) {
			return new Spending(
				$spending["id_spending"],
				$spending["type_spending"],
				$spending["date_spending"],
				$spending["qty_spending"],
				$spending["description_spending"],
				$spending["doc_name_spending"],
				$spending["file_name_on_server"],
				new User($spending["owner_spending"])
			);
		} else {
			return NULL;
		}
	}



	/**
	 * Saves a Spending into the database
	 *
	 * @param Spending $sp The spending to be saved
	 * @throws PDOException if a database error occurs
	 * @return int The new spending id
	 */
	public function save(Spending $sp)
	{
		$stmt = $this->db->prepare("INSERT INTO spending(type_spending,date_spending,qty_spending,description_spending,doc_name_spending,file_name_on_server,owner_spending) values (?,?,?,?,?,?,?)");
		$stmt->execute(array(
			$sp->getType(), $sp->getDate(), $sp->getQuantity(), $sp->getDescription(),
			$sp->getDocName(), $sp->getDocServer(), $sp->getOwner()->getAlias()
		));

		return $this->db->lastInsertId();
	}

	/**
	 * Updates a Spending in the database
	 *
	 * @param Spending $sp The spending to be updated
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function update(Spending $sp)
	{
		$stmt = $this->db->prepare("UPDATE spending set type_spending=?, date_spending=?, qty_spending=?, description_spending=?, doc_name_spending=?, file_name_on_server=? ,owner_spending=? where id_spending=?");
		$stmt->execute(array(
			$sp->getType(), $sp->getDate(), $sp->getQuantity(),
			$sp->getDescription(), $sp->getDocName(),
			$sp->getDocServer(),
			$sp->getOwner()->getAlias(),
			$sp->getId()
		));
	}

	/**
	 * Deletes a Spending from the database
	 *
	 * @param Spending $sp The spending to be deleted
	 * @throws PDOException if a database error occurs
	 * @return void
	 */
	public function delete(Spending $sp)
	{
		$stmt = $this->db->prepare("DELETE from spending WHERE id_spending=?");
		$stmt->execute(array($sp->getId()));

		return true;
	}


	/**
	 * Retrieves all Spendings that
	 * @param string $alias The alias of the current user
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of MonthSpending instances 
	 */
	public function findMonthSpendings($alias, $fecha_inicio, $fecha_fin, $types)
	{

		$queryFindSpendings = "SELECT type_spending, MONTH(date_spending), YEAR(date_spending), SUM(qty_spending)
			FROM spending, users
			WHERE date_spending between ? and ? and
				users.alias=spending.owner_spending and users.alias=? and type_spending IN (?,?,?,?,?)
			GROUP BY type_spending, MONTH(date_spending), YEAR(date_spending)
			ORDER by type_spending, YEAR(date_spending), MONTH(date_spending)";

		$stmt = $this->db->prepare($queryFindSpendings);
		$stmt->execute(array($fecha_inicio, $fecha_fin, $alias, $types[0], $types[1], $types[2], $types[3], $types[4]));
		$spending_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$month_spendings = array();

		foreach ($spending_db as $spending) {
			$ms = new MonthSpending(
				$spending["type_spending"],
				$spending["MONTH(date_spending)"],
				$spending["YEAR(date_spending)"],
				$spending["SUM(qty_spending)"]
			);
			array_push($month_spendings, $ms);
		}

		return $month_spendings;
	}
}
