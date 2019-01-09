// JavaScript Document

//location checkboxes
$(document).ready(function(){
    $("#loc-list .mainloc").change(function(){
	
	var $cbloc = $(this).attr('id');
	
      $("." + $cbloc).prop('checked', $(this).prop("checked"));
      });
});