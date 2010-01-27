<?php defined('SYSPATH') or die('No direct script access.');

class WizardForm_Wizard extends View {

	protected $views = array();
	protected $session;
	protected $before_render = array();

	public function  __construct(array $views, $formaction = '') {
		parent::__construct('wizard');
		$this->views = $views;
		$this->wizard = array(
			'formaction' => $formaction,
			'steps' => count($views),
			'title' => '',
			'subtitle' => '',
			'save_enabled' => NULL,
		);
		$this->var = array();
		$this->session = Session::instance();
	}
	
	public function execute(array $initdata = array()) {
		$is_next = Arr::get($_POST, 'wizard:next');
		$is_prev = Arr::get($_POST, 'wizard:prev');
		$is_save = Arr::get($_POST, 'wizard:save');
		$wizid = Arr::get($_POST, 'wizard:wizid');
		$this->wizard['from_step'] = Arr::get($_POST, 'wizard:step',0);
		$this->wizard['step'] = $this->wizard['from_step'];
		$presave = $this->session->get("wizard:$wizid");
		$form = array();
		if (!$presave) {
			$presave = $initdata;
			$wizid = Text::random('alnum', 8);
		}
		$return = array(
			'button' => NULL,
			'post' => NULL,
		);
		if ($is_next OR $is_prev OR $is_save) {
			if ($is_next AND $this->wizard['step'] < $this->wizard['steps']) {
				$this->wizard['step']++;
			}
			if ($is_prev AND $this->wizard['step'] > 0) {
				$this->wizard['step']--;
			}
			foreach ($_POST as $k => $v) {
				if (substr($k, 0, 7) != 'wizard:') {
					$form[$k] = $v;
					$presave[$k] = $v;
				}
			}
			$return['button'] = ($is_prev? 'prev' : ($is_next? 'next':'save'));
		}
		$this->post = $presave;
		$this->wizard['wizid'] = $wizid;
		$this->session->set("wizard:$wizid", $presave);
		$return['form'] = $form;
		$return['post'] = $presave;
		$return['next_step'] = $this->wizard['step'];
		$return['cur_step'] = $this->wizard['from_step'];
		return $return;
	}

	public function title($title, $subtitle = '') {
		$this->wizard = array(
			'title' => $title,
			'subtitle' => $subtitle,
		);
		return $this;
	}

	public function set_data(array $data, $merge = TRUE) {
		$this->post = ($merge? array_merge($this->post, $data) : $data);
		$this->session->set("wizard:{$this->wizard['wizid']}", $this->post);
		return $this;
	}

	public function set_var($key, $value) {
		$this->var[$key] = $value;
		return $this;
	}

	public function set_var_array(array $vars, $merge = TRUE) {
		$this->var = ($merge? array_merge($this->var, $vars) : $vars);
		return $this;
	}

	public function enable_save($enabled) {
		$this->wizard['save_enabled'] = $enabled;
		return $this;
	}
	
	public function replace_view($view, $step) {
		if ($step <= $this->wizard['steps']) {
			$this->views[$step] = $view;
			if ($step == $this->wizard['steps']) {
				$this->wizard['steps']++;
			}
			return true;
		}
		return false;
	}

	public function show_error($msg, $set_step = NULL) {
		$this->wizard['errormsg'] = $msg;
		$this->wizard['step'] = (!is_null($set_step)? $set_step : Arr::get($this->wizard, 'from_step', $this->wizard['step']));
		return $this;
	}

	public function render($file = NULL) {
		if (is_null($this->wizard['save_enabled'])) {
			$this->wizard['save_enabled'] = ($this->wizard['step'] + 1 == $this->wizard['steps']);
		}
		$current = $this->views[$this->wizard['step']];
		$this->wizard['content'] = new View($current);
		$this->wizard['content']->post = $this->post;
		$this->wizard['content']->wizard = $this->wizard;
		$this->wizard['content']->var = $this->var;
		foreach ($this->before_render as $callback) {
			call_user_func($callback, &$this->wizard['content']);
		}
		return parent::render($file);
	}

	public function register_before_render($callback) {
		$this->before_render[] = $callback;
	}

}