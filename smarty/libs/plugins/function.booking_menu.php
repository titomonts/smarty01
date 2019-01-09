<?php
function smarty_function_booking_menu($params, &$smarty)
{
    global $data;
    
    $defValue = false; 
    if ((isset($params['booking']) && $params['booking'] == 'TRUE') && isset($params['urls'])) {
        $url = explode(',', $params['urls']);
        foreach($url as $u) {
            $get_redirect = explode('=>', trim($u));
            if (strpos($get_redirect[0], '/') > -1) {
                $get_parent = explode('/', $get_redirect[0]);
                $parent = $get_parent[count($get_parent)-2];
                $page_identifier = $get_parent[count($get_parent)-1];
            } else {
                $parent = false;
                $page_identifier = $get_redirect[0];
            }
            $set_id = $data['villa_id'];
            $set_redirect = $get_redirect[1];
            if (strpos($get_redirect[1], ':') > -1) {
                $get_id = explode(':', $get_redirect[1]);
                $set_id = $get_id[0];
                $set_redirect = $get_id[1];
            }
            if (strpos($set_id, '|') > -1) {
                if (isset($params['page']['page_identifier']) && $params['page']['page_identifier'] == $page_identifier) {
                    if (($parent && $params['page']['parent'] == $parent) || !$parent) {
                        $defValue = '/' . $page_identifier . '.html?villaid=' . $set_id . '&curl=' . $data['home_uri'] . '/' . $set_redirect . '.html';
                    }
                } elseif ($params['page'] == $page_identifier) {
                    $defValue = '/' . $page_identifier . '.html?villaid=' . $set_id . '&curl=' . $data['home_uri'] . '/' . $set_redirect . '.html';
                }
            } else {
                if (isset($params['page']['page_identifier']) && $params['page']['page_identifier'] == $page_identifier) {
                    if (($parent && $params['page']['parent'] == $parent) || !$parent) {
                        $defValue = 'http://booking.elitehavens.com/booking.aspx?pid=' . $set_id . '&curl=' . $data['home_uri'] . '/' . $set_redirect . '.html';
                    }
                } elseif ($params['page'] == $page_identifier) {
                    $defValue = 'http://booking.elitehavens.com/booking.aspx?pid=' . $set_id . '&curl=' . $data['home_uri'] . '/' . $set_redirect . '.html';
                } 
            }           
        }
    }
    if ($params['return']) {
        return $defValue;
    }
    $smarty->assign($params['assign'], $defValue); 
}