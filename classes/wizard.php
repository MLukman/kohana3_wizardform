<?php defined('SYSPATH') or die('No direct script access.');

class Wizard extends View {

	protected $views = array();
	protected $session;

	public function  __construct(array $views, $title = 'Untitled', $subtitle = '') {
		parent::__construct('wizard');
		$this->views = $views;
		$this->wizard = array(
			'steps' => count($views),
			'title' => $title,
			'subtitle' => $subtitle,
		);
		$this->session = Session::instance();
	}

	public function execute(array $initdata = array()) {
		$is_next = Arr::get($_POST, 'wizard:next');
		$is_prev = Arr::get($_POST, 'wizard:prev');
		$is_save = Arr::get($_POST, 'wizard:save');
		$wizid = Arr::get($_POST, 'wizard:wizid');
		$cur_step = Arr::get($_POST, 'wizard:step',0);
		$next_step = $cur_step;
		$presave = $this->session->get("wizard:$wizid");
		if (!$presave) {
			$presave = $initdata;
			$wizid = Text::random('alnum', 8);
		}
		$return = array(
			'button' => NULL,
			'post' => NULL,
		);
		if ($is_next OR $is_prev OR $is_save) {
			if ($is_next AND $next_step < $this->wizard['steps']) {
				$next_step++;
			}
			if ($is_prev AND $next_step > 0) {
				$next_step--;
			}
			foreach ($_POST as $k => $v) {
				if (substr($k, 0, 7) != 'wizard:') {
					$presave[$k] = $v;
				}
			}
			$return['button'] = ($is_prev? 'prev' : ($is_next? 'next':'save'));
		}
		$this->post = $presave;
		$this->wizard['wizid'] = $wizid;
		$this->wizard['step'] = $next_step;
		$this->session->set("wizard:$wizid", $presave);
		$return['post'] = $presave;
		$return['next_step'] = $next_step;
		$return['cur_step'] = $cur_step;
		return $return;
	}

	public function set_data(array $data) {
		$this->post = $data;
		$this->session->set("wizard:{$this->wizard['wizid']}", $data);
	}
	
	public function replace_view($view, $step) {
		$this->views[$step] = $view;
	}

	public function show_error($msg, $set_step = NULL) {
		$this->wizard['errormsg'] = $msg;
		if (!is_null($set_step)) {
			$this->wizard['step'] = $set_step;
		}
	}

	public function render($file = NULL) {
		$current = $this->views[$this->wizard['step']];
		$this->wizard['content'] = new View($current);
		$this->wizard['content']->post = $this->post;
		$this->wizard['content']->wizard = $this->wizard;
		return parent::render($file);
	}

}