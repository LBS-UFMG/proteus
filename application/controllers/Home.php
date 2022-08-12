<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index($view='home', $data = array()){
		$this->template->show($view,$data);
	}
}