<?php

namespace Addon\TURLS;

defined('is_running') or die('Not an entry point...');


class TURLS{

	function __construct(){
		global $page, $addonPathData;
		$this->url = preg_replace('/'.$page->title.'\/?/', '', $page->requested, 1);
		$this->urls = \gpFiles::Get($addonPathData.'/urls.php', 'urls');

		if(!isset($this->urls[$this->url]))
			switch(\common::LoggedIn()){
				case true: return $this->Stat();
				case false: return \gp\tool::Redirect(\gp\tool::AbsoluteUrl(), $code = 301);
			}

		$this->Stat($this->url);
		\gp\tool::Redirect($this->urls[$this->url], $code = 301);
	}

	function Stat(){
		global $addonPathData;
		$statFile = $addonPathData.'/stat.php';
		$this->stats = \gpFiles::Get($statFile, 'stats');
		if(!isset($this->urls[$this->url]))
			return;
		$this->stats[$this->url] = isset($this->stats[$this->url]) ? $this->stats[$this->url]+1 : $this->stats[$this->url] = 1;
		\gpFiles::SaveArray($statFile,'stats',$this->stats);
	}
}