<?php
function smarty_function_booking($params, &$smarty)
{
    global $data, $mdata;
    
    $all_config_vars = $smarty->getConfigVars();

    switch ($params['template']) {
        case 'form':
            
        /*{booking_menu page='reservations' urls=#BOOKING_ENGINE_URL# booking=#BOOKING_ENGINE# assign="linkout"}
        {if ! $linkout} {assign var='linkout' value=("{$data.home_uri}/reservations.html")} {/if}
        {booking template="form" curl="reservation-sent" action=$linkout}*/
        
        $method = 'post';
        $arrival = 'txtPreArriveDate';
        $departure = 'txtPreDepartDate';
        preg_match("/http:\/\/[a-z]+\.elitehavens\.com/", $params['action'], $output_array);
        if (count($output_array) > 0) {
            $method = 'get';
            $arrival = 'ci';
            $departure = 'co';
        }
?>
        <div class="availability_calendar_form">
            <form action="<?php echo $params['action']; ?>" method="<?php echo $method; ?>" target="bookingTarget" class="miniform">
                <input type="hidden" id="villaID" name="pid" value="<?php echo $data['villa_id']; ?>">
                <input type="hidden" id="curl" name="curl" value="<?php echo $data['home_uri']; ?>/<?php echo $params['curl']; ?>.html">
                <?php if (sizeof($mdata['Rooms']) > 1 && count($output_array) == 0) { ?>
                <div class="form-container">
                    <label for="bedroom">Bedrooms:</label>
                    <div class="form-column form-select">
                        <select name="bedroom" id="bedroom">
                            <?php 
                            for ($i = 0; $i < sizeof($mdata['Rooms']); $i++) { 
                                if ($params['br'] ==  floatval($mdata['Rooms'][$i]['RoomName'])) {
                            ?>
                            <option value="<?php echo floatval($mdata['Rooms'][$i]['RoomName']); ?>" selected><?php echo $mdata['Rooms'][$i]['RoomName']; ?></option>
                                <?php } else { ?>
                            <option value="<?php echo floatval($mdata['Rooms'][$i]['RoomName']); ?>"><?php echo $mdata['Rooms'][$i]['RoomName']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php } ?>
                <div class="form-container">
                    <label for="txtArrivalDate_plugin">Date:</label>
                    <div class="form-column">
                        <input name="<?php echo $arrival; ?>" type="text" id="txtArrivalDate_plugin" class="inputbox" readonly="readonly" placeholder="Check-in" value="<?php echo $params['ci']; ?>" />
                        <input name="<?php echo $departure; ?>" type="text" id="txtDepartDate_plugin" class="inputbox" readonly="readonly" placeholder="Check-out" value="<?php echo $params['co']; ?>" />
                    </div>
                </div>
                <button type="submit">Enquire</button>
            </form>
        </div>
<?php
        break;
        case 'availability': 
?>      <div class="iframe-wrapper iframe-booking">
                <div class="availability_calendar_checker">
                    <form action="about:blank" method="get" target="bookingTarget">
                        <input type="hidden" id="villaID" name="pid" value="<?php echo !empty($params['villa_id']) ? $params['villa_id'] : $data['villa_id']; ?>">
                        <input type="hidden" id="curl" name="curl" value="<?php echo $data['home_uri']; ?>/<?php echo $params['curl']; ?>.html">        
                        <div class="availability_calendar_form-input" id="input-dates">            
                            <div class="availability_calendar_form-control"> 
                                <span class="availability_calendar_input"> 
                                    <label for="txtArrivalDate">check-in</label> 
                                    <input type="text" name="ci" id="<?php echo !empty($params['villa_id']) ? $params['villa_id'] : $data['villa_id']; ?>txtArrivalDate_plugin" readonly required="">  
                                </span> 
                                <span class="availability_calendar_input">                    
                                    <label for="txtDepartDate">check-out</label>                    
                                    <input type="text" name="co" id="<?php echo !empty($params['villa_id']) ? $params['villa_id'] : $data['villa_id']; ?>txtDepartDate_plugin" readonly required="">                
                                </span>                
                                <button type="submit">Check availability</button>            
                            </div>        
                        </div>    
                    </form>    
                </div>
        </div>
<?php
        break;
        case 'loader':
?>
<div class="availability_calendar_checker">
    <div class="availability_calendar_popup availability_calendar_redirect_popup">
        <div class="availability_calendar_overlay"></div>
        <div class="availability_calendar_popup_content">
            <h2><strong>Thank you</strong>, almost there...</h2>
            <div class="availability_calendar_arrows">
                <span id="arrow-1"></span>
                <span id="arrow-2"></span>
                <span id="arrow-3"></span>
            </div>
            <p>We’re taking you to<br />our enquiry form.</p>
            <a href="http://www.elitehavens.com" target="_blank"><img src="/resources/common/bookingbar/images/logo-eh.png" /><small>experts in luxury villa holidays</small></a>
        </div>
    </div>
</div>

<?php
    $params['booking'] = $all_config_vars['BOOKING_ENGINE'];
    $params['urls'] = $all_config_vars['BOOKING_ENGINE_URL'];
    $params['return'] = true;
    include_once(SMARTY_PATH . 'plugins/function.booking_menu.php');
    $url = explode(',', $params['urls']);
    $tg = array();
    $or = array();
    foreach($url as $u) {
        $get_redirect = explode('=>', trim($u));
        $origin = '/' . $get_redirect[0] . '.html';
        $params['page'] = $get_redirect[0];
        $redirect = smarty_function_booking_menu($params, $smarty);
        if (substr($redirect, 0, 1) == '/') {
            $redirect = $data['home_uri'] . $redirect;
        }
        array_push($tg, $redirect);
        array_push($or, $origin);
    }    
?>
<script type="text/javascript">
var tg = ["<?php echo implode('","', $tg); ?>"];
var or = ["<?php echo implode('","', $or); ?>"];
var elems = document.getElementsByTagName('a');
for (var i = 0; i < elems.length; i++) {
    for (var x = 0; x < or.length; x++) {
        if (elems[i]['href'] == '<?php echo $data['home_uri']; ?>' + or[x])
        elems[i]['href'] = elems[i]['href'].replace('<?php echo $data['home_uri']; ?>' + or[x] + '', tg[x]);
    }
}
</script>
<?php
        break;
?>
<?php
    }
}