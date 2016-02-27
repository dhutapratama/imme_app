<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array("web_download_url", "file_versions"));
		$this->load->helper('download');
	}

	public function index()
	{
		$this->_error('114', 'Error request url', FALSE);
	}

	public function android($download_code = '')
	{
		if ($download_code == '') {
			redirect();
		} else {
			$download = $this->web_download_url->select_by_download_code($download_code);
			if ($download) {
				$file = $this->file_versions->select_by_id($download[0]->version_id);

				if ($file) {
					$download_url['download_count']		= $download[0]->download_count + 1;
					$download_url['last_download_date']	= date("Y-m-d H:i:s");
					$this->web_download_url->update_by_download_code($download[0]->download_code, $download_url);

					$file_version['total_download']	= $file[0]->total_download + 1;
					$this->file_versions->update_by_id($file[0]->version_id, $file_version);

					force_download('apk/'.$file[0]->filename, NULL);

					echo "Your download is starting ...";
				} else {
					echo "Your file is doesn't exist!";
				}
				
			} else {
				$this->_error('file_doesnt_exist', 'Error downloading file', FALSE);
			}
		}
	}

	private function _error($code = '100', $message = 'Unknown error', $csrf = TRUE)
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		if($csrf) {
			$json['csrf_token']	= $this->security->get_csrf_hash();
		}
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
	}
}
