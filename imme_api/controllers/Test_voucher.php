<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_voucher extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->library(array('Ciqrcode'));
        $this->load->helper(array('url', 'file'));
        $this->load->model('deposit_vouchers');
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
		exit();
	}

	public function create($amount = 1)
	{
		$this->load->library('image_lib');
		$voucher_code = $this->deposit_vouchers->create_voucher($amount);
		$params['data'] = $voucher_code;
		$params['level'] = 'H';
		$params['size'] = 200;
		$params['savename'] = FCPATH.'vouchers/'.$voucher_code.'.png';
		$this->ciqrcode->generate($params);

		$wm1['source_image']		= './vouchers/imme-voucher-template.png';
		$wm1['wm_overlay_path']		= './vouchers/'.$voucher_code.'.png';
		$wm1['new_image']			= './vouchers/'.$voucher_code.'.png';
		$wm1['wm_type']				= 'overlay';
		$wm1['wm_opacity']			= '100';
		$wm1['wm_vrt_alignment']	= 'top';
		$wm1['wm_hor_alignment']	= 'left';
		$wm1['wm_vrt_offset']		= '87';
		$wm1['wm_hor_offset']		= '4';
		$this->image_lib->initialize($wm1);
		$this->image_lib->watermark();

		$wm2['source_image']		= $wm1['new_image'];
		$wm2['new_image']			= $wm1['new_image'];
		$wm2['wm_text']				= 'Rp';
		$wm2['wm_type']				= 'text';
		$wm2['wm_font_path']		= './fonts/HelveticaNeue-Light.otf';
		$wm2['wm_font_size']		= '16';
		$wm2['wm_font_color']		= 'ffffff';
		$wm2['wm_vrt_alignment']	= 'top';
		$wm2['wm_hor_alignment']	= 'left';
		$wm2['wm_vrt_offset']		= '262';
		$wm2['wm_hor_offset']		= '62';
		$wm2['wm_padding']			= '1';

		$this->image_lib->initialize($wm2);
		$this->image_lib->watermark();

		$wm3['source_image']		= $wm1['new_image'];
		$wm3['new_image']			= $wm1['new_image'];
		$wm3['wm_text']				= $amount;
		$wm3['wm_type']				= 'text';
		$wm3['wm_font_path']		= './fonts/HelveticaNeue-Light.otf';
		$wm3['wm_font_size']		= '27';
		$wm3['wm_font_color']		= 'ffffff';
		$wm3['wm_vrt_alignment']	= 'top';
		$wm3['wm_hor_alignment']	= 'left';
		$wm3['wm_vrt_offset']		= '262';
		$wm3['wm_hor_offset']		= '87';
		$wm3['wm_padding']			= '1';

		$this->image_lib->initialize($wm3);
		$this->image_lib->watermark();

		echo '<img src="http://'.base_url().'vouchers/'.$voucher_code.'.png" /><br />';
	}

	private function _error($code = '100', $message = 'Unknown error')
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
	}

	public function test_create() {
		$i = 0;
		while (true) {
			if ($i == 100) {
				break;
			}
			$voucher_code = $this->deposit_vouchers->create_voucher(987);
			$i++;
		}
		echo "100 Insert was finished";
	}

	public function test_select() {
		$i = 0;
		while (true) {
			if ($i == 100) {
				break;
			}
			$this->deposit_vouchers->select();
			$i++;
		}
		echo "100 Select was finished";
	}

}
