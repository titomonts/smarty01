// JavaScript Document

var url="http://"+window.location.hostname;

$(document).ready(function(){

	$('#page-info').css('width','100%');
	
	$('select').selectBox({
			mobile: true,
			menuSpeed: 'fast',
			menuTransition: 'fade'
		});
	
	/* Script for Menu */
	$( "#burger" ).click(function() {
		$( "#sub-burger" ).slideToggle( "normal" );
	});
	
	
	/*********** Thankyou popup for Contact-us form *********/
	var contact_status = $( "#contact_status" ).attr('value');
	
	if( contact_status == 'yes'){
		var contactstat = $( "#thankyou-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:570
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#thankyou-popup .close').click(function () {
			contactstat.dialog( "close" );
		});
	}
	else
		{ $('#thankyou-popup').css('display','none'); }
	/********* End Thankyou popup *********/
	
	
	
	/********* Enquiry sent popup *********/
	var enquiry_sent = $( "#enquiry_sent" ).attr('value');
	
	if( enquiry_sent == 'yes'){
		var enq_sent = $( "#enquiry-sent-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:570
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#enquiry-sent-popup .close').click(function () {
			enq_sent.dialog( "close" );
		});
	}
	else
		$('#enquiry-sent-popup').css('display','none');
	/********* End Enquiry sent popup *********/
	
	
	
	/********* SignUp Message *********/
	var signup_msg = $( "#signup_msg" ).attr('value');
	
	if( signup_msg != ''){
		var signup_pop = $( "#sign-up-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:650
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#sign-up-popup .close').click(function () {
			signup_pop.dialog( "close" );
		});
	}
	else
		$('#sign-up-popup').css('display','none');
	/********* End SignUp Message *********/
	
	
	/********* Forgot Pass message *********/
	var forgotPass_popup = $( "#forgotPass_popup" ).attr('value');
	
	if( forgotPass_popup != ''){
		var forgot_pop = $( "#forget-pass-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:650
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#forget-pass-popup .close').click(function () {
			forgot_pop.dialog( "close" );
		});
	}
	else
		$('#forget-pass-popup').css('display','none');
	/********* End Forgot Pass message *********/
	
	
	/********* Sign In message *********/
	var signin_popup = $( "#signin_popup" ).attr('value');
	
	if( signin_popup != ''){
		var signin_pop = $( "#sign-in-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:570
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#sign-in-popup .close').click(function () {
			signin_pop.dialog( "close" );
		});
	}
	else
		$('#sign-in-popup').css('display','none');
	/********* End Sign In message *********/
	
	
	/********* Sign In message *********/
	var edit_prof = $( "#edit_prof" ).attr('value');
	
	if( edit_prof != ''){
		var edit_pop = $( "#edit-prof-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:570
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#edit-prof-popup .close').click(function () {
			edit_pop.dialog( "close" );
		});
	}
	else
		$('#edit-prof-popup').css('display','none');
	/********* End Sign In message *********/
	
	/********* Invalid Captcha *********/
	var inv_captcha = $( "#inv_captcha" ).attr('value');
	
	if( inv_captcha != ''){
		var cap_pop = $( "#inv-captcha-popup" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:570
		});
	
		$(".ui-dialog-titlebar").hide();
		$('#inv-captcha-popup .close').click(function () {
			cap_pop.dialog( "close" );
		});
	}
	else
		$('#inv-captcha-popup-popup').css('display','none');
	/********* Invalid Captcha *********/
	
	
	
	
	/* Load correct Price depending on default currency */
	if( $("#prop_page").length > 0 )
	{
		var propCur = $("#hidPropCurrency").val();
		var r_usd = parseFloat($("#rate_usd").val());
		var r_idr = parseFloat($("#rate_idr").val());
		var h_price = parseFloat($("#hidPrice").val());
		var l_price = $("#hidListedPrice").val();
		var convert = 0;
		
		if( $("#top-cur-selector").val() == 3 && propCur == 3 )
		{
			convert = 'USD '+( h_price ).formatMoney(0,'.',',');
		}
		if( $("#top-cur-selector").val() == 4 && propCur == 4 )
		{
			convert = 'IDR '+( h_price ).formatMoney(0,'.','.');
		}
		
		if( $("#top-cur-selector").val() == 3 && propCur == 4 )
		{
			convert = 'USD '+( h_price*r_usd ).formatMoney(0,'.',',');
		}
		if( $("#top-cur-selector").val() == 4 && propCur == 3 )
		{
			convert = 'IDR '+( h_price*r_idr ).formatMoney(0,'.','.');
		}
		
		if( l_price != 'Upon Request' )
		{
			$("#presyong_kaibigan").html(convert);
		}
		else
		{
			$("#presyong_kaibigan").html('Upon Request');
		}
		/* You may also like */
		var others = $("#other_props").val().split(',');
		for(var o=0; o<others.length; o++)
		{
			var oCur = $("#ymal_cur_"+others[o]).val();
			var hPr = parseFloat($("#ymal_pr_"+others[o]).val());
			var hLp = $("#ymal_lp_"+others[o]).val();
			var oConvert = '';
			if( $("#top-cur-selector").val() == 3 && oCur == 3 )
			{
				oConvert = 'USD '+( hPr ).formatMoney(0,'.',',');
			}
			if( $("#top-cur-selector").val() == 4 && oCur == 4 )
			{
				oConvert = 'IDR '+( hPr ).formatMoney(0,'.','.');
			}
			
			if( $("#top-cur-selector").val() == 3 && oCur == 4 )
			{
				oConvert = 'USD '+( hPr*r_usd ).formatMoney(0,'.',',');
			}
			if( $("#top-cur-selector").val() == 4 && oCur == 3 )
			{
				oConvert = 'IDR '+( hPr*r_idr ).formatMoney(0,'.','.');
			}
			
			if( hLp != 'Upon Request' )
			{
				$("#change_"+others[o]).html(oConvert);
			}
			else
			{
				$("#change_"+others[o]).html('Upon Request');
			}
			
			delete oCur;
			delete hPr;
			delete hLp;
			delete oConvert;
		}
		/* End you may also like */
		
		/* Check if the property is part of user's property list */
		if( $("#mem_is_logged").length > 0 )
		{
			var prop_list = $("#memProps").val();
			var prop_id = $("#pid").val();
			if( prop_list.indexOf(prop_id) >= 0 )
			{
				$("#add-to-my-property").css({"border-color":"#7aad3e","color":"#000"}).html("<div class='wish'><input type='hidden' id='del_me' name='del_me' value='SI' /></div>Remove from<br /> My Properties");
			}
		}
		/* End check if the property is part of user's property list */
	}
	/* End load correct price */
	
	/* Start find property of owner and highlight star */
	var prop_list = $("#memProps").val().split(',');
	for( var p=0; p<prop_list.length; p++ )
	{
		$("#"+prop_list[p]).html('<span class="str-white" title="'+prop_list[p]+'"></span>');
	}
	/* End find property of owner and highlight star */
	
	/* Start add to property list */
	$('body').delegate("dd span.star","click",function(){
		var prop = $(this).prop("title");
		var mem = $("#mem_id").val();
		//$(this).removeClass('star').addClass('str-white').attr('title',prop);
		if( mem != '' )
		{
			$.ajax({
				type: "POST",
				url: "/ajax/my_precious.php",
				data:'&memid='+mem+'&pid='+prop+'&action=add',
				error: function(){
					alert('Adding property to list error')
				},
				success: function(data){
					if(data == 'OK')
					{
						$("dd[id='"+prop+"']").html('<span class="str-white" title="'+prop+'"></span>');
					}
					else
					{
						alert(data);
					}
				}
			});
		}
		else
		{
			var subscribe = $( "#login-first" ).dialog({
				modal: true,
				resizable: false,
				show: 'slide',
				width:650
			});
			
			$("#login-first .close").on("click", function() {
				subscribe.dialog( "close" );
			});
			$(".ui-dialog-titlebar").hide();
		}
	});
	/* End add to property list */
	
	/* Start remove from property list */
	$('body').delegate("dd span.str-white","click",function(){
		var prop = $(this).prop("title");
		var mem = $("#mem_id").val();
		//$(this).removeClass('str-white').addClass('star').attr('title',prop);
		$.ajax({
			type: "POST",
			url: "/ajax/my_precious.php",
			data:'&memid='+mem+'&pid='+prop+'&action=remove',
			error: function(){
				alert('Removing property from list error')
			},
			success: function(data){
				if(data == 'OK')
				{
					$("dd[id='"+prop+"']").html('<span class="star" title="'+prop+'"></span>');
					if( $("#prof_url").length > 0 )
					{
						$("#my_"+prop).animate({opacity:0}).hide('slow', function(){ 
							$("#my_"+prop).remove();
							location.reload();  
						});
					}
				}
				else
				{
					alert(data);
				}
			}
		});
		
	});
	/* End remove from property list */
});


/* Start add to property list */
$('body').delegate("dd > span.star","click",function(){	
	var prop = $(this).prop("title");
	var mem = $("#mem_id").val();
	
	if( mem != '' )
	{
		$.ajax({
			type: "POST",
			url: "/ajax/my_precious.php",
			data:'&memid='+mem+'&pid='+prop+'&action=add',
			error: function(){
				alert('Adding property to list error')
			},
			success: function(data){
				if(data == 'OK')
				{
					$('body').find("dd[id='"+prop+"']").html('<span class="str-white" title="'+prop+'"></span>');
				}
				else
				{
					alert(data);
				}
			}
		});
	}
	else
	{
		var subscribe = $( "#login-first" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:650
		});
		
		$("#login-first .close").on("click", function() {
			subscribe.dialog( "close" );
		});
		$(".ui-dialog-titlebar").hide();
	}
});
/* End add to property list */

/* Start remove from property list */
$('body').delegate("dd > span.str-white","click",function(){
	var prop = $(this).prop("title");
	var mem = $("#mem_id").val();
	
	$.ajax({
		type: "POST",
		url: "/ajax/my_precious.php",
		data:'&memid='+mem+'&pid='+prop+'&action=remove',
		error: function(){
			alert('Removing property from list error')
		},
		success: function(data){
			if(data == 'OK')
			{
				$("dd[id='"+prop+"']").html('<span class="star" title="'+prop+'"></span>');
				if( $("#prof_url").length > 0 )
				{
					$("#my_"+prop).animate({opacity:0}).hide('slow', function(){ 
						$("#my_"+prop).remove();
						location.reload(); 
					});
				}
			}
			else
			{
				alert(data);
			}
		}
	});
	
});
/* End remove from property list */

/* Profile Page Property lists pagination */
$('body').delegate('#pagination .paginate','click', function(){
	var page = $(this).attr('page-data');
	$.ajax({
		type: "POST",
		url: "/loadProps.php",
		data:'&page='+page,
		error: function(){
			alert('Page Error')
		},
		success: function(data){
			$("#myProperties").html(data);
		}
	});
});
/* End Profile Page Property lists pagination */

/* Start currency post back */
$('#top-cur-selector').change(function(){
	$("#post-back-url").val(window.location.pathname);
	if( $("#frmPropSearch").length > 0 || $("#frm-adv-search").length > 0 )
	{
		$("#hidCur").val($('#top-cur-selector').val());
		$("#quick-search").submit();
	}
	/* Start when page is in property details page */
	if( $("#prop_page").length > 0 )
	{
		var propCur = $("#hidPropCurrency").val();
		var r_usd = parseFloat($("#rate_usd").val());
		var r_idr = parseFloat($("#rate_idr").val());
		var h_price = parseFloat($("#hidPrice").val());
		var l_price = $("#hidListedPrice").val();
		var convert = 0;
		
		if( $(this).val() == 3 && propCur == 3 )
		{
			convert = 'USD '+( h_price ).formatMoney(0,'.',',');
		}
		if( $(this).val() == 4 && propCur == 4 )
		{
			convert = 'IDR '+( h_price ).formatMoney(0,'.','.');
		}
		
		if( $(this).val() == 3 && propCur == 4 )
		{
			convert = 'USD '+( h_price*r_usd ).formatMoney(0,'.',',');
		}
		if( $(this).val() == 4 && propCur == 3 )
		{
			convert = 'IDR '+( h_price*r_idr ).formatMoney(0,'.','.');
		}
		
		if( l_price != 'Upon Request' )
		{
			$("#presyong_kaibigan").html(convert);
		}
		else
		{
			$("#presyong_kaibigan").html('Upon Request');
		}
		
		/* You may also like */
		var others = $("#other_props").val().split(',');
		for(var o=0; o<others.length; o++)
		{
			var oCur = $("#ymal_cur_"+others[o]).val();
			var hPr = parseFloat($("#ymal_pr_"+others[o]).val());
			var hLp = $("#ymal_lp_"+others[o]).val();
			var oConvert = '';
			if( $(this).val() == 3 && oCur == 3 )
			{
				oConvert = 'USD '+( hPr ).formatMoney(0,'.',',');
			}
			if( $(this).val() == 4 && oCur == 4 )
			{
				oConvert = 'IDR '+( hPr ).formatMoney(0,'.','.');
			}
			
			if( $(this).val() == 3 && oCur == 4 )
			{
				oConvert = 'USD '+( hPr*r_usd ).formatMoney(0,'.',',');
			}
			if( $(this).val() == 4 && oCur == 3 )
			{
				oConvert = 'IDR '+( hPr*r_idr ).formatMoney(0,'.','.');
			}
			
			if( hLp != 'Upon Request' )
			{
				$("#change_"+others[o]).html(oConvert);
			}
			else
			{
				$("#change_"+others[o]).html('Upon Request');
			}
			
			delete oCur;
			delete hPr;
			delete hLp;
			delete oConvert;
		}
		/* End you may also like */
		
		/* Dynamically register new currency in session */
		$.ajax({
			type: "POST",
			url: "/ajax/change_currency.php",
			data:'&cur='+$(this).val(),
			error: function(){
				alert('Registering session failed')
			},
			success: function(data){
			}
		})
		/* End Dynamically register new currency in session */
	}
	/* End when page is in property details page */
	if( $("#myProperties").length > 0 )
	{
		/* Dynamically register new currency in session */
		$.ajax({
			type: "POST",
			url: "/ajax/change_currency.php",
			data:'&cur='+$(this).val(),
			error: function(){
				alert('Registering session failed')
			},
			success: function(data){
				location.reload();
			}
		})
		/* End Dynamically register new currency in session */
		
	}
});
Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
/* End currency post back */

/* This is used in property details page */
if( $("#prop_page").length > 0 )
{
	window.onload = function () {
		var lat = document.getElementById('lat').value;
		var lng = document.getElementById('lng').value;
		var radius = document.getElementById('radius').value;
		var zoom = parseInt(document.getElementById('zoom').value);
		
		var styles = [
			{
			  "featureType": "landscape.man_made",
			  "stylers": [{ "color": "#e2e1df" }]
			},
			{
			  "featureType": "water",
			  "stylers": [{ "weight": 2.5 },{ "color": "#a7b3bf" }]
			},
			{
			  "featureType": "road",
			  "stylers": [{ "visibility": "on" }]
			}
		];
		
		var opts = {
		mapTypeControlOptions: {
		  mapTypeIds: ['Styled']
		},
		center: new google.maps.LatLng(lat,lng),
		zoom: zoom,
		disableDefaultUI: false,  
		mapTypeId: 'Styled',
		scrollwheel: false,
		draggable: false, zoomControl: false, scrollwheel: false, disableDoubleClickZoom: true
		};
		var div = document.getElementById('elite');
		var map = new google.maps.Map(div, opts);
		var styledMapType = new google.maps.StyledMapType(styles, { name: 'Bali Property Sales' });
		map.mapTypes.set('Styled', styledMapType);
		
		
		icon_img="/media/images/icomap.png";  
		var ico = new google.maps.MarkerImage(icon_img,  new google.maps.Size(84,58),
		new google.maps.Point(0,0), new google.maps.Point(100,25));  
		
		
		/*
		var marker = new google.maps.Marker({
		  map: map,
		  icon: ico,
		  position: new google.maps.LatLng(lat,lng),
		  draggable: false,
		});
		*/
		
		var myCircle = new google.maps.Circle({
		  center: new google.maps.LatLng(lat,lng),
		  map: map,
		  radius: radius*1.2,
		  strokeColor: "green",
		  strokeOpacity: 0.8,
		  strokeWeight: 1,
		  fillColor: "#D0FFD0",
		  fillOpacity: 0.35
		});
	
	
	}
}

if($('div > #galleria').length>0){
	Galleria.loadTheme('/js/galleria.classic.min.js');
	Galleria.run('div > #galleria',{swipe:true,thumbQuality:false,imageCrop:true,responsive:true,transition:'flash',transitionSpeed:1000,autoplay:5000});
	$('#galleria').fadeTo('slow',1);
}

$("#add-to-my-property").on("click", function(){
	if( $("#mem_is_logged").length <= 0 )
	{
		var subscribe = $( "#login-first" ).dialog({
			modal: true,
			resizable: false,
			show: 'slide',
			height:'auto',
			width:650
		});
		
		$('#login-first .close').click(function () {
			subscribe.dialog( "close" );
		});
		$(".ui-dialog-titlebar").hide();
	}
	else
	{
		var mem = $("#mem_is_logged").val();
		var prop = $("#pid").val();
		$("#add-to-my-property").animate({opacity:0.2});
		
		if( $("#del_me").length <= 0 )
		{
			$.ajax({
				type: "POST",
				url: "/ajax/my_precious.php",
				data:'&memid='+mem+'&pid='+prop+'&action=add',
				error: function(){
					alert('Adding property to list error')
				},
				success: function(data){
					if(data == 'OK')
					{
						$("#add-to-my-property").animate({
						  opacity: 1,
						  top: "2.5"
						}, 900, function() {
						  $("#add-to-my-property").css({"border-color":"#7aad3e","color":"#000"}).fadeIn(900).html("<div class='wish'><input type='hidden' id='del_me' name='del_me' value='SI' /></div>Remove from<br /> My Properties");
						});
					}
					else
					{
						alert(data);
					}
				}
			})
		}
		else
		{
			$.ajax({
				type: "POST",
				url: "/ajax/my_precious.php",
				data:'&memid='+mem+'&pid='+prop+'&action=remove',
				error: function(){
					alert('Adding property to list error')
				},
				success: function(data){
					if(data == 'OK')
					{
						$("#add-to-my-property").animate({
						  opacity: 1,
						  top: "2.5"
						}, 900, function() {
						  $("#add-to-my-property").fadeIn(900).html("<div class='wish'></div>My Properties");
						});
					}
					else
					{
						alert(data);
					}
				}
			})
		}
		
	}
});
/* End used in property details page */

/* Sign-In Pop-Up */
$('.sign-up').on("click", function(){
	var subscribe = $( "#signup" ).dialog({
		modal: true,
		resizable: false,
		show: 'slide',
		height:'auto',
		width:570
	});
	
	$('#signup .close').click(function () {
		subscribe.dialog( "close" );
	});
	$(".ui-dialog-titlebar").hide();
});
/* End Sign-In Pop-Up */






/***** Inquiry Pop-Up *********/

$('.m-enquiry').on("click", function(){
	var subscribe = $( "#inquiry" ).dialog({
		modal: true,
		resizable: false,
		show: 'slide',
		height:'auto',
		width:570
	});
	
	$('#inquiry .close').click(function () {
		subscribe.dialog( "close" );
	});
	$(".ui-dialog-titlebar").hide();
});

	/*** Enquire - from Villa listings ***/
$('.btn-enquire').on("click", function(){
	
	var hdesc = $(this).attr('hdesc');
	var vref = $(this).attr('vref');
	var idprop = $(this).attr('idprop');
	
	$('#idprop').val(idprop);
	$('#villapagelink').val(window.location.href);
	
	$('h2#prop_header').html(hdesc + " - #" + vref);
	var subscribe = $( "#inquiry" ).dialog({
		modal: true,
		resizable: false,
		show: 'slide',
		height:'auto',
		width:570
	});
	
	$('#inquiry .close').click(function () {
		subscribe.dialog( "close" );
	});
	$(".ui-dialog-titlebar").hide();
});

if( $('#frmInquiry').length > 0 )
{
	$('.try').on("click",function() {
		$('.captcha').prop("src","/media/capca/captcha.php");
	});
}
/***** End Inqury Pop-Up ******/






/* Start For Forgot Password */

if( $('#frmForgotPass').length > 0 )
{
	$('body').delegate('a.change','click', function(){
		$('#captchanator').html('<img src="/media/capca/captcha.php" class="captcha" style="height:52px; "/>');
	})
	/*
	$('.change').on("click",function() {
		$('#captchanator').html('<img src="/media/capca/captcha.php" class="captcha" style="height:52px; "/>');
	});
	*/
}
/* End For Forgot Password */

/* Form Validators */
if( $('#frmNewsLetter').length > 0 )
{
	$.validate({
		form : '#frmNewsLetter',
		validateOnBlur : false,
		errorMessagePosition : 'top',
		scrollToTopOnError : false
	});
}
if( $('#frmListProp').length > 0 )
{
	$.validate({
		form: '#frmListProp',
		validateOnBlur: false,
		errorMessagePosition: 'top',
		scrollToTopOnError : false
	});
}
if( $('#frmSignIn').length > 0 )
{
	$.validate({
		form: '#frmSignIn',
		validateOnBlur: false,
		errorMessagePosition: 'top',
		scrollToTopOnError : false
	});
}
if( $('#frmJoinUs').length > 0 )
{
	$.validate({
		form: '#frmJoinUs',
		validateOnBlur: false,
		errorMessagePosition: 'top',
		scrollToTopOnError : false
	});
}
if( $('#frmInquiry').length > 0 )
{
	$.validate({
		form: '#frmInquiry',
		validateOnBlur: false,
		errorMessagePosition: 'top',
		scrollToTopOnError : false
	});
}
if( $('#frmForgotPass').length > 0 )
{
	$.validate({
		form: '#frmForgotPass',
		validateOnBlur: false,
		errorMessagePosition: 'top',
		scrollToTopOnError : false
	});
}
/* End Form Validators */


/*** Join our Newletter ***/
$('#frmNewsLetter').submit( function(e){
	
	var nama = $('#fullname').val();
	var email = $('#emailadd').val();
	
	$.post(""+url+"/newsletter.php?action=newsletter", { "nama": nama, "email": email},
	
	function(data) {
		if(data==0){
		
			var nlt1 = $( ".nlt_error" ).dialog({
				modal: true,
				resizable: false,
				show: 'slide',
				height:'auto',
				width:580
			});
			
			$(".ui-dialog-titlebar").hide();
			$('.nlt_error .close').click(function () {
				nlt1.dialog( "close" );
			});
		}
		else {
			var nlt2 = $( ".nlt_standar" ).dialog({
				modal: true,
				resizable: false,
				show: 'slide',
				height:'auto',
				width:570
			});
		
			$(".ui-dialog-titlebar").hide();
			$('.nlt_standar .close').click(function () {
				nlt2.dialog( "close" );
			});
		}
	});
	
	e.preventDefault();
});


$(document).ready(function() {
    // Configure/customize these variables.
    var showChar = 250;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Read More";
    var lesstext = "Read Less";
    

    $('.paragraphp2').each(function() {
        var content = $(this).html();

        if(content.length > showChar) {
 
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);
 
            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent">' + h + '</span>';
 
            $(this).html(html);
        }
 
    });
 
    $(".read_more").click(function(){
        if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(moretext);
        } else {
            $(this).addClass("less");
            $(this).html(lesstext);
        }
        $('.moreellipses').toggle();
        $('.morecontent').toggle('slow');
        return false;
    });
});
/*** End Join our newsletter ***/

/* Sort listing */
function post_this()
{
	var order = $("#selSort").val();
	$.ajax({
		type: "POST",
		url: "/ajax/set_order.php",
		data:'&sort='+order,
		error: function(){
			alert('Registering sort order failed')
		},
		success: function(data){
			if(data == 'OK')
			{
				location.reload(); 
			}
		}
	})
}

/* Start Email Validation via Webservice */
if( $("#frm-contact-us").length > 0 || $("#frmJoinUs").length > 0 || $("#frmForgotPass").length > 0 || $("#frmInquiry").length > 0)
{
	$("#txtEmail").blur(function(e) {
        var email = $(this).val();
		$('.spinner').show();
		$.ajax({
			type: "POST",
			url: "/ajax/validate_email.php",
			data:'&e='+email,
			error: function(e){
				return false;
				e.preventDefault();
			},
			success: function(data){
				if(data == 'OK')
				{
					return true;
					$("#txtEmail").css("border-color","#0F0").delay(1500).removeAttr("style");
				}
				else
				{
					var subscribe = $( "#inv_email" ).dialog({
						modal: true,
						resizable: false,
						show: 'slide',
						height:'auto',
						width:570
					});
					$('#inv_email .close').click(function () {
						subscribe.dialog( "close" );
					});
					$(".ui-dialog-titlebar").hide();
					
					$("#txtEmail").val('').css("border-color","red").delay(1500).removeAttr("style");
					return false;
				}
			},
			complete: function(){
				$('.spinner').hide();
			  }
		})
    });
}
/* End Email Validation via Webservice */

/** Select Country ---- Required validation **/
$('form#frmInquiry').submit( function(e){
	if( $("#selCountry").val() == null ) {
		$("a.selCtry").css('box-shadow','0px 0px 1px 1px #F00');
		e.preventDefault();
	}
});
$('form#frmListProp').submit( function(e){
	if( $("#selCountry").val() == null ) {
		$("a.selCtry").css('box-shadow','0px 0px 1px 1px #F00');
		e.preventDefault();
	}
});
$('form#frm-contact-us').submit( function(e){
	if( $("#selCountry").val() == null ) {
		$("a.selCtry").css('box-shadow','0px 0px 1px 1px #F00');
		e.preventDefault();
	}
});
$('form#frmJoinUs').submit( function(e){
	if( $("#selCountry").val() == null ) {
		$("a.selCtry").css('box-shadow','0px 0px 1px 1px #F00');
		e.preventDefault();
	}
});



$('#selCountry').change( function() {
	if( $("#selCountry").val() != null ) {
		$('.selCtry').css('box-shadow','none');
		}
});
/** End Select Country **/