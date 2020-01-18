<?php

namespace Addon\TURLS;

defined('is_running') or die('Not an entry point...');

class TURLS_Admin extends \Addon\TURLS\TURLS{

	function __construct(){
		global $addonRelativeCode, $title, $page, $addonPathCode, $config, $langmessage;
		$page->head_js[] = $addonRelativeCode.'/TURLS.js';
		$page->css_admin[] = $addonRelativeCode.'/TURLS.css';
		$lang = \gpFiles::Get($addonPathCode.'/languages/'.$config['language'].'.php', 'lang') ?: \gpFiles::Get($addonPathCode.'/languages/en.php', 'lang');
		TURLS::__construct();

		if(\common::GetCommand() == 'SaveURLs')
			$this->SaveURLs();

		echo '<h2 class="text-center">'.$langmessage['Admin UI'].'</h2>';

		echo '<table id="turls" class="bordered">';
		echo '<th>'.$lang['Original URL'].'</th>';
		echo '<th>'.$lang['Short URL'].'</th>';
		echo '<th>'.$lang['Clicks'].'</th>';
		echo '<th>'.$lang['Actions'].'</th>';
		echo '<form id="urls" action="'.\common::GetUrl($title).'" method="post">';
		echo '<tr><td><input type="url" name="furls[]" required placeholder="https://sestolab.pp.ua"/></td>';
		echo '<td><input name="surls[]" placeholder="sestolab"/></td>';
		echo '<td></td>';
		echo '<td>
				<input type="hidden" name="cmd" value="SaveURLs" />
				<button type="submit" title="'.$lang['Shorten URL'].'"><span class="fa fa-cut">&nbsp;</span>'.$lang['Shorten URL'].'</button>
			</td></tr>';

		foreach ($this->urls as $k=>$v){
			echo '<tr>';
			echo '<td><a href="'.$v.'">'.$v.'</a>
						<input type="hidden" name="furls[]" value="'.$v.'" readonly/>
					</td>';
			echo '<td>'.\gp\tool::Link($page->gp_index.'/'.$k, $k).'
						<input type="hidden" name="surls[]" value="'.$k.'" readonly/>
					</td>';
			echo '<td>'.(isset($this->stats[$k])?$this->stats[$k]:0).'</td>';
			echo '<td class="actions">
					<button type="button" title="'.$langmessage['edit'].'" at="'.$langmessage['save'].'"><span class="fa fa-pencil"></span></button>
					<button type="button" title="'.$langmessage['delete'].'" at="'.$langmessage['cancel'].'" confirm="'.sprintf($langmessage['generic_delete_confirm'], $k).'"><span class="fa fa-trash"></span></button>
					</td>';
			echo '</tr>';
		}
		echo '</form></table>';

		echo '<div class="text-right">Made by <a href="https://sestolab.pp.ua" target="_blank">Sestolab</a></div>';
	}

	function SaveURLs(){
		global $addonPathData, $langmessage;
		$urls = array();

		for($i = 0; $i < count($_POST['furls']); $i++){
			if(empty($_POST['furls'][$i]))
				continue;
			if(empty(trim($_POST['surls'][$i])))
				$_POST['surls'][$i] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
			$urls[\gp\tool::LabelSpecialChars($_POST['surls'][$i])] = \gp\tool::UrlChars($_POST['furls'][$i]);
		}
		foreach($this->stats as $i => $k)
			if(!isset($urls[$i]))
			   unset($this->stats[$i]);
		\gpFiles::SaveArray($addonPathData.'/stat.php','stats', $this->stats);
		$this->urls = $urls;
		if(!\gpFiles::SaveData($addonPathData.'/urls.php','urls', $urls)){
			message($langmessage['OOPS']);
			return false;
		}
		message($langmessage['SAVED']);
		return true;
	}
}
