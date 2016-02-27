<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Imme {

	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->library(array("ciqrcode", "image_lib"));
		$CI->load->model(array("deposit_vouchers", "accounts", "balances", "receive_sessions"));
	}

	public function sim_voucher($amount = 500, $merchant_id = 1)
	{
		$CI =& get_instance();
		$code = md5(time().rand(1000, 9999));
		$params['data'] = $code;
		$params['level'] = 'H';
		$params['size'] = 200;
		$params['savename'] = FCPATH.'storage/vouchers/'.$code.'.png';
		$CI->ciqrcode->generate($params);

		$deposit_vouchers['voucher_code']	= $code;
		$deposit_vouchers['amount']			= $amount;
		$deposit_vouchers['created_date']	= date("Y-m-d H:i:s");
		$deposit_vouchers['merchant_id']	= $merchant_id;
		$deposit_vouchers['is_used']		= 0;
		$deposit_vouchers['is_simulation']	= 1;
		$CI->deposit_vouchers->insert($deposit_vouchers);

		$wm1['source_image']		= './storage/template/voucher.png';
		$wm1['wm_overlay_path']		= './storage/vouchers/'.$code.'.png';
		$wm1['new_image']			= './storage/vouchers/'.$code.'.png';
		$wm1['wm_type']				= 'overlay';
		$wm1['wm_opacity']			= '100';
		$wm1['wm_vrt_alignment']	= 'top';
		$wm1['wm_hor_alignment']	= 'left';
		$wm1['wm_vrt_offset']		= '87';
		$wm1['wm_hor_offset']		= '4';
		$CI->image_lib->initialize($wm1);
		$CI->image_lib->watermark();
		$CI->image_lib->clear();

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
		$CI->image_lib->initialize($wm2);
		$CI->image_lib->watermark();
		$CI->image_lib->clear();

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
		$CI->image_lib->initialize($wm3);
		$CI->image_lib->watermark();
		$CI->image_lib->clear();

		$output['code'] = $code;
		$output['url'] = base_url('storage/vouchers/'.$code.'.png');

		return $output;
	}

	public function sim_payment()
	{
		$CI =& get_instance();
		$code = md5(time().rand(1000, 9999));
		$params['data'] = $code;
		$params['level'] = 'H';
		$params['size'] = 200;
		$params['savename'] = FCPATH.'storage/point_of_sales/'.$code.'.png';
		$CI->ciqrcode->generate($params);

		$merchant_id = 1;

		$account = $CI->accounts->select_by_merchant_id($merchant_id);
		$balance = $CI->balances->select_by_merchant_id($merchant_id);
		
		$receive_sessions['transaction_code']		= $code;
		$receive_sessions['merchant_id']			= 1;
		$receive_sessions['account_id']				= $account[0]->account_id;
		$receive_sessions['balance_id']				= $balance[0]->balance_id;
		$receive_sessions['amount']					= 12350;
		$receive_sessions['transaction_type_id']	= 8;
		$receive_sessions['created_date']			= date("Y-m-d H:i:s");
		$receive_sessions['expired_date']			= date("Y-m-d H:i:s", (time() + 3600 * 24));
		$receive_sessions['transaction_status_id']	= 1;
		$receive_sessions['is_simulation']			= 1;
		$CI->receive_sessions->insert($receive_sessions);

		$wm2['source_image']		= './storage/template/cashier.png';
		$wm2['wm_overlay_path']		= './storage/point_of_sales/'.$code.'.png';
		$wm2['new_image']			= './storage/point_of_sales/'.$code.'.png';
		$wm2['wm_type']				= 'overlay';
		$wm2['wm_opacity']			= '100';
		$wm2['wm_vrt_alignment']	= 'top';
		$wm2['wm_hor_alignment']	= 'right';
		$wm2['wm_vrt_offset']		= '241';
		$wm2['wm_hor_offset']		= '72';
		$CI->image_lib->initialize($wm2);
		$CI->image_lib->watermark();
		$CI->image_lib->clear();

		$output['code'] = $code;
		$output['url'] = base_url('storage/point_of_sales/'.$code.'.png');

		return $output;
	}

	public function sim_collect()
	{
		$CI =& get_instance();
		$code = md5(time().rand(1000, 9999));
		$params['data'] = $code;
		$params['level'] = 'H';
		$params['size'] = 250;
		$params['savename'] = FCPATH.'storage/payment_receipt/'.$code.'.png';
		$CI->ciqrcode->generate($params);

		$resize['source_image'] = './storage/payment_receipt/'.$code.'.png';
		$resize['maintain_ratio'] = TRUE;
		$resize['width']         = 125;

		$CI->image_lib->initialize($resize);
		$CI->image_lib->resize();

		$wm3['source_image']		= './storage/template/receipt.png';
		$wm3['wm_overlay_path']		= './storage/payment_receipt/'.$code.'.png';
		$wm3['new_image']			= './storage/payment_receipt/'.$code.'.png';
		$wm3['wm_type']				= 'overlay';
		$wm3['wm_opacity']			= '100';
		$wm3['wm_vrt_alignment']	= 'top';
		$wm3['wm_hor_alignment']	= 'left';
		$wm3['wm_vrt_offset']		= '255';
		$wm3['wm_hor_offset']		= '14';
		$CI->image_lib->initialize($wm3);
		$CI->image_lib->watermark();
		$CI->image_lib->clear();
		
		$output['code'] = $code;
		$output['url'] = base_url('storage/payment_receipt/'.$code.'.png');

		return $output;
	}
}