<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'third_party/Business/TodoManager.php';
require APPPATH.'third_party/DataAccess/'.DB_LAYER.'/'.DB_LAYER.'TodoDal.php';

class Todo extends CI_Controller {

	private $todoManager;
	private $todoDal;
	private $dbLayer;

	public function __construct() {
		parent ::__construct();
		header('Access-Control-Allow-Origin: *');
		$this->dbLayer = DB_LAYER.'TodoDal';
		$this->todoDal = new $this->dbLayer();
		$this->todoManager = new TodoManager($this->todoDal);
	}

	public function index()
	{
		output($this->todoManager->alltodos());
	}

	public function add()
	{
		output($this->todoManager->add());
	}

	public function update($id)
	{
		output($this->todoManager->update($id));
	}

	public function done()
	{
		output($this->todoManager->done());
	}

	public function delete()
	{
		output($this->todoManager->delete());
	}

}
