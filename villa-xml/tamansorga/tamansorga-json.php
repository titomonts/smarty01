<?php
$json = '[
			{
				"menu-label":"Home",
				"template":"home-template.html",
				"content_id":"INTRO",
				"page_identifier":""
			},
			{
				"menu-label":"Villa",
				"template":"page-template.html",
				"content_id":"ABTV",
				"page_identifier":"about-villa-tamansorga",
				"children":[
								{"label":"About Villa Taman Sorga","template":"page-template.html","content_id":"ABTV","page_identifier":"about-villa-tamansorga"},
								{"label":"Villa Layout","template":"page-template.html","content_id":"LYT","page_identifier":"villa-layout"},
								{"label":"Bedrooms","template":"page-template.html","content_id":"BDR","page-identifier":"bedrooms"},
								{"label":"Living Areas","template":"page-template.html","content_id":"LVA","page_identifier":"living-areas"},
								{"label":"Floorplan","template":"page-template.html","content_id":"","page_identifier":"floorplan"}
							]
			},
			{		
				"menu-label":"Gallery",
				"template":"photo-gallery.html",
				"content_id":"",
				"page_identifier":"photo-gallery",
				"children":[
								{"label":"Photo Gallery","template":"photo-gallery.html","content_id":"","page_identifier":"photo-gallery"},
								{"label":"Floorplan","template":"page-template.html","content_id":"","page_identifier":"floorplan"},
								{"label":"Bedrooms","template":"page-template.html","content_id":"","page-identifier":"virtual-tour"}
							]
			},
			{
				"menu-label":"Details",
				"template":"page-template.html",
				"content_id":"QF",
				"page_identifier":"quick-facts",
				"children":[
								{"label":"Quick Facts","template":"page-template.html","content_id":"QF","page_identifier":"quick-facts"},
								{"label":"Dining","template":"page-template.html","content_id":"DNNG","page_identifier":"dining"},
								{"label":"Staff","template":"page-template.html","content_id":"STAFF","page_identifier":"staff"},
								{"label":"Families","template":"page-template.html","content_id":"FAM","page_identifier":"families"},
								{"label":"Spa &amp; Gym","template":"page-template.html","content_id":"SPAGYM","page_identifier":"spa"}	
							]
			},
			{
				"menu-label":"Location",
				"template":"page-template.html",
				"content_id":"LCTN",
				"page_identifier":"the-locale",
				"children":[
								{"label":"The Locale","template":"page-template.html","content_id":"LCTN","page_identifier":"the-locale"},
								{"label":"Things to do","template":"page-template.html","content_id":"TTD","page_identifier":"things-to-do"}		
							]
			},
			{
				"menu-label":"Reviews",
				"template":"page-template.html",
				"content_id":"",
				"page_identifier":"guest-reviews"
			},
			{
				"menu-label":"Rates",
				"template":"page-template.html",
				"content_id":"",
				"page_identifier":"rates"
			},
			{
				"menu-label":"Availability",
				"template":"page-template",
				"content_id":"",
				"page_identifier":"availability"
			},
			{
				"menu-label":"Enquire",
				"template":"page-reservation.html",
				"content_id":"",
				"page_identifier":"reservations"
			},
			{
				"menu-label":"Contact",
				"template":"page-template.html",
				"content_id":"CONTACTS",
				"page_identifier":"contact-us"
			}
		]';