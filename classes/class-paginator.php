<?php

class Paginator{
	function paginate($reload, $page, $tpages) {
		$adjacents = 3;
		$prevlabel = "&laquo; ";
		$nextlabel = " &raquo;";
		$out = "";
		// previous
		if ($page > 1) {
			$out.="<a href=\"".$reload."/".($page - 1)."\" onclick=\"return seo_pagination(".($page - 1).")\">".$prevlabel."</a>";
		} else {
			$out.= "<strong style='padding:3px;'>".$prevlabel."</strong>\n";
		}
		$pmin=($page>$adjacents)?($page - $adjacents):1;
		$pmax=($page<($tpages - $adjacents))?($page + $adjacents):$tpages;
		for ($i = $pmin; $i <= $pmax; $i++) {
			if ($i == $page) {
				$out.= "<strong>".$i."</strong>";
			} elseif ($i == 1) {
				$out.= "<a href='".$reload."/".$i."' onclick=\"return seo_pagination('1')\">".$i."</a>";
			} else {
				$out.= "<a href=\"".$reload. "/".$i."\" onclick=\"return seo_pagination(".$i.")\">".$i. "</a>";
			}
		}
		
		if ($page<($tpages - $adjacents)) {
			$out.= "<a style='font-size:11px' href=\"" . $reload."/".$tpages."\" onclick=\"return seo_pagination(".$tpages.")\">...</a>\n";
		}
		// next
		if ($page < $tpages) {
			$out.= "<a href=\"".$reload."/".($page + 1)."\" onclick=\"return seo_pagination(".($page + 1).")\">".$nextlabel."</a>";
		} else {
			$out.= "<strong style='padding:3px;'>".$nextlabel."</strong>";
		}
		$out.= "";
		return $out;
	}
}