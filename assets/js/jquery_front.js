jQuery(document).ready(function( $ ) {
    $('.full_rent_form2').hide();
	var dateToday = new Date();	
	var min_book = $('#pravel_min_book_required').val();
	var disable_dates_json = $('#pravel_disable_dates_ser_json').val();
	var disabledDates = $.parseJSON( disable_dates_json );
	var booked_by_user = $('#pravel_booked_by_user').val();
	var booked_by_user_ar = $.parseJSON( booked_by_user );	
	
	var disabledDates_final = [];
	var bookedDates_final = [];
	var bookedDates_final_qty = [];
	var buy_qty = $('input[name="quantity"]').attr('max');
	var i = 0;
	$.each(disabledDates, function( index, value ) {
		var from_date = disabledDates[index]['from_date'];
		var from_date = formatDate(from_date);
		var from_date_1 = new Date(formatDate(from_date));
		var to_date = disabledDates[index]['to_date'];		
		var to_date = new Date(formatDate(to_date));
		while(from_date_1 <= to_date)
		{
			disabledDates_final.push(from_date);
			from_date_1 = new Date(from_date_1.getTime() + 24 * 60 * 60 * 1000);
			from_date = formatDate(from_date_1);
			from_date_1 = new Date(formatDate(from_date));
		}	
	});
	
	$.each(booked_by_user_ar, function( index, value ) {
		var booked_from_date = booked_by_user_ar[index]['from_date'];
		var booked_from_date = formatDate(booked_from_date);
		var booked_from_date_1 = new Date(formatDate(booked_from_date));
		var booked_to_date = booked_by_user_ar[index]['to_date'];		
		var qty = booked_by_user_ar[index]['qty'];		
		var booked_to_date = new Date(formatDate(booked_to_date));
		while(booked_from_date_1 <= booked_to_date)
		{
			var array_with_qty = [];
			array_with_qty['date'] = booked_from_date;
			array_with_qty['qty'] = qty;
			bookedDates_final.push(booked_from_date);
			bookedDates_final_qty.push(array_with_qty);
			booked_from_date_1 = new Date(booked_from_date_1.getTime() + 24 * 60 * 60 * 1000);
			booked_from_date = formatDate(booked_from_date_1);
			booked_from_date_1 = new Date(formatDate(booked_from_date));
		}	
	});

	var dates = $(".pravel_front_start_date, .pravel_front_end_date").datepicker({			  
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm/dd/yy',
		numberOfMonths: 1,
		beforeShowDay: function(date){
			
			var startDate = $('.pravel_front_start_date').datepicker('getDate');			
			var endDate = $('.pravel_front_end_date').datepicker('getDate');
			var string = jQuery.datepicker.formatDate('yy-mm-dd', date);		
			if(startDate != null){				
				if (date >= startDate && date <= endDate) {				
					return [true, 'ui-datepicker-selected_custom-day', ''];
				}
			}
			return [ disabledDates_final.indexOf(string) == -1 ];
			
		},
		minDate: dateToday,			
		onSelect: function(selectedDate) {	
			
			$('.pravel_date_error').hide();
			$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', false);
			var name_attr = $(this).attr('date-type'); 	
			
			if(name_attr == 'pravel_front_start_date'){
			    
				var option = 'minDate';
				var from_date = selectedDate;
				$(".pravel_front_end_date").val('');
				var to_date = '';
				var instance = $(this).data("datepicker"),
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);				
			}
			else
			{
				var option = 'maxDate';
				var from_date = $(".pravel_front_start_date").val();
				var to_date = selectedDate;
			
			}
					
			
			
			if(name_attr == 'pravel_front_start_date'){
				var from_date = selectedDate;
				var to_date = $(".pravel_front_end_date").val();	
				var form_date_chage = formatDate(from_date);
				var min_book = $('#pravel_min_book_required').val();
				
				var days = $('#block_days').val();
				if(min_book != 0 && min_book != '')
				{
					min_book = min_book - 1;					
					var start_date_sel = new Date(from_date);
					var nextday_on_min_range = new Date(from_date);
					nextday_on_min_range.setDate(start_date_sel.getDate() + parseInt(min_book));				
					$(".pravel_front_end_date").datepicker('option', 'minDate', nextday_on_min_range);
				}
		
				var filter_date_ar = [];
				for(var j = 0; j < disabledDates_final.length; j++)
				{
					if(form_date_chage < disabledDates_final[j])
					{
						filter_date_ar.push(new Date(disabledDates_final[j]));
					}
				}				
				var minDate_t = new Date(Math.min.apply(null,filter_date_ar));	
				
				$(".pravel_front_end_date").datepicker('option', 'maxDate', minDate_t);

				if(days != ''&& days != undefined) {
					
					
					days = days - 1; 					
					var start_date_sel = new Date(from_date);
					start_date_sel.setDate(start_date_sel.getDate() + parseInt(days));								
					$(".pravel_front_end_date").datepicker('setDate', start_date_sel);	
					
					var getDateArray = function(start, end) {
                        var arr = new Array();
                        var dt = new Date(start);
                        while (dt <= end) {
                            arr.push(new Date(dt));
                            dt.setDate(dt.getDate() + 1);
                        }
                        return arr;
                    }
                    
                    var dateArr = getDateArray(from_date, start_date_sel);
                    
                    var formatedDateArr = [];
                    for(var i = 0; i< dateArr.length; i++ ) {
                        var date = new Date(dateArr[i]);
                        var newDate = formatDate(date);
                        formatedDateArr.push(newDate);
                    }
                    
                    var matches = [];

                    $.grep(formatedDateArr, function(el) {
                    
                        if ($.inArray(el, disabledDates_final) != -1) {
                            matches.push(el);
                        }
                    
                    });
                    
                    if (matches.length > 0) {
                        $('#has_disabled_value').html("Selected Dates are not avaiable.");
                        $('.pravel_front_start_date').val('');
                        dates.datepicker('setDate', null);
                     
                    } else {
                        $('#has_disabled_value').html("");
                    }
					
					var qty_sum = 0;
					if(booked_by_user_ar != null && booked_by_user_ar != '')
					{
						for(var j = 0; j < booked_by_user_ar.length; j++)
						{
							var booked_from_date = booked_by_user_ar[j]['from_date'];
							var booked_from_date = formatDate(booked_from_date);
							var booked_to_date = booked_by_user_ar[j]['to_date'];
							var booked_to_date = formatDate(booked_to_date);
							var from_date_new = formatDate(from_date);
							var to_date_new = formatDate(start_date_sel);
							
							if(((from_date_new >= booked_from_date && from_date_new <= booked_to_date) || (to_date_new >= booked_from_date && to_date_new <= booked_to_date)) || ((booked_from_date >= from_date_new && booked_from_date <= to_date_new) || (booked_to_date >= from_date_new && booked_to_date <= to_date_new)))
							{	
								var qty_booked = booked_by_user_ar[j]['qty'];
								qty_sum += parseInt(qty_booked);							
							}
						}	
											
						
						var rent_qty = $('#pravel_book_product_qty_nochange').val();	
						var left_qty = parseInt(rent_qty) - parseInt(qty_sum);
						$('#inner_form_qty_rent').val(left_qty);
						if(left_qty <= 0)
						{
							$('input[name="quantity"]').attr('max', 0);
							$('#pravel_book_product_qty').val(0);
							$('#pravel_check_out_general_pro_2').removeClass('in-stock');
							$('#pravel_check_out_general_pro_2').addClass('out-of-stock');
							$('#pravel_check_out_general_pro_2').text('0 in stock');
							
							$('.pravel_date_error').html('<p>Product Not Available on this duration</p>');
							$('.pravel_date_error').show();	
							var from_date = $(".pravel_front_start_date").val();
						    var to_date = $('.pravel_front_end_date').val();
						    $('#start_unavaiable_hidden_date').val(from_date);
						    $('#end_unavaiable_hidden_date').val(to_date);
							$('.full_rent_form2').show();
							$('.full_rent_form').hide();
							$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', true);
						}
						else
						{
							$('input[name="quantity"]').attr('max', left_qty);
							$('#pravel_book_product_qty').val(left_qty);
							$('#pravel_check_out_general_pro_2').removeClass('out-of-stock');
							$('#pravel_check_out_general_pro_2').addClass('in-stock');
							$('#pravel_check_out_general_pro_2').text(left_qty + ' in stock');
						}								
					}
                    
				}
				
				
			}
			
			var from_date_condition_check = new Date(formatDate(from_date));				
			var to_date_condition_check = new Date(formatDate(to_date));
			if(from_date_condition_check <= to_date_condition_check)
			{	
		
				
				var selected_dates = [];				
				var from_date_selected = formatDate(from_date);
				var from_date_1 = new Date(formatDate(from_date_selected));				
				var to_date_selected = new Date(formatDate(to_date));
				var check_int = 0;
				
				var pravel_booking_extra_price_option = $('#pravel_booking_extra_price_option').val();
				var pravel_extra_price_date = $('#pravel_extra_price_date').val();
				var pravel_extra_price_date_ar = $.parseJSON( pravel_extra_price_date );	
				var pravel_extra_price_days = $('#pravel_extra_price_days').val();
				var pravel_extra_price_days_ar = $.parseJSON( pravel_extra_price_days );
				var pravel_custom_price_dates_eve_month = $('#pravel_custom_price_dates_eve_month').val();
				
				
				
				var pravel_extra_price_same_option = $('#pravel_extra_price_same_option').val();
				
				var pravel_custom_price_dates_eve_month_ar = $.parseJSON( pravel_custom_price_dates_eve_month );
				
				
				
				var extra_price_date_ne_ar = [];
				var k = 0;
				$.each(pravel_extra_price_date_ar, function( index, value ) {
					var from_date = pravel_extra_price_date_ar[index]['from_date'];
					var from_date = formatDate(from_date);
					var from_date_123 = new Date(formatDate(from_date));
					var to_date = pravel_extra_price_date_ar[index]['to_date'];		
					var to_date = new Date(formatDate(to_date));
					var price = pravel_extra_price_date_ar[index]['price'];		
					while(from_date_123 <= to_date)
					{
						
						extra_price_date_ne_ar[k] = {		
							date:from_date_123,
							price:price,							
						}
						
						from_date_123 = new Date(from_date_123.getTime() + 24 * 60 * 60 * 1000);
						from_date = formatDate(from_date_123);
						from_date_123 = new Date(formatDate(from_date));
						k++;
					}	
				});	
				
				
				var total = 0;
			
				while(from_date_1 <= to_date_selected)
				{
								
					var weekday = new Array(7);
					weekday[0] = "sunday";
					weekday[1] = "monday";
					weekday[2] = "tuesday";
					weekday[3] = "wednesday";
					weekday[4] = "thursday";
					weekday[5] = "friday";
					weekday[6] = "saturday";					
					var days = weekday[from_date_1.getDay()];
					var select_date = from_date_1.getDate();					
					
					selected_dates.push(from_date_selected);
					var disable_count = disabledDates_final.indexOf(formatDate(from_date_selected));					
					if (disable_count != -1)
					{
						check_int++;
					}
					from_date_1 = new Date(from_date_1.getTime() + 24 * 60 * 60 * 1000);
					from_date_selected = formatDate(from_date_1);
					from_date_1 = new Date(formatDate(from_date_selected));	
					var base_book_price = parseFloat($('#pravel_booking_base_price').val());			
					total += parseFloat(base_book_price);
				}
				total
				$('#pravel_booking_price').val(total);				
				if(check_int > 0)
				{
					$('.pravel_date_error').html('<p>Select Vaild Daterange</p>');
					$('.pravel_date_error').show();
					$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', true);
					return;
				}
				else
				{
					$('.pravel_date_error').hide();
					$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', false);
				}				
				var total_days = pravel_datediff(pravel_parseDate(from_date), pravel_parseDate(to_date));
				var min_book = $('#pravel_min_book_required').val();
				if(total_days < min_book)
				{
					$('.pravel_date_error').html('<p>Required '+min_book+' days min booking</p>');
					$('.pravel_date_error').show();
					$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', true);
				}
				else{					
					$('.pravel_date_error').hide();
					$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', false);
				}
				
								
				var qty_sum = 0;
				if(booked_by_user_ar != null && booked_by_user_ar != '')
				{
					for(var j = 0; j < booked_by_user_ar.length; j++)
					{
						var booked_from_date = booked_by_user_ar[j]['from_date'];
						var booked_from_date = formatDate(booked_from_date);
						var booked_to_date = booked_by_user_ar[j]['to_date'];
						var booked_to_date = formatDate(booked_to_date);
						var from_date_new = formatDate(from_date);
						var to_date_new = formatDate(to_date);
						
						if(((from_date_new >= booked_from_date && from_date_new <= booked_to_date) || (to_date_new >= booked_from_date && to_date_new <= booked_to_date)) || ((booked_from_date >= from_date_new && booked_from_date <= to_date_new) || (booked_to_date >= from_date_new && booked_to_date <= to_date_new)))
						{	
							var qty_booked = booked_by_user_ar[j]['qty'];
							qty_sum += parseInt(qty_booked);							
						}
					}	
					$('.stock').addClass('pravel_rent');					
					var rent_qty = $('#pravel_book_product_qty_nochange').val();	
					var left_qty = parseInt(rent_qty) - parseInt(qty_sum);
					$('#inner_form_qty_rent').val(left_qty);
					if(left_qty <= 0)
					{
						$('input[name="quantity"]').attr('max', 0);
						$('#pravel_book_product_qty').val(0);
						$('#pravel_check_out_general_pro_2').removeClass('in-stock');
						$('#pravel_check_out_general_pro_2').addClass('out-of-stock');
						$('#pravel_check_out_general_pro_2').text('0 in stock');
						
						$('.pravel_rent').removeClass('in-stock');
						$('.pravel_rent').addClass('out-of-stock');
						$('.pravel_rent').text('0 in stock');
						
						$('.pravel_date_error').html('<p>Product Not Available on this duration</p>');
						$('.pravel_date_error').show();
						var from_date = $(".pravel_front_start_date").val();
						var to_date = $('.pravel_front_end_date').val();
						$('#start_unavaiable_hidden_date').val(from_date);
						$('#end_unavaiable_hidden_date').val(to_date);
						$('.full_rent_form2').show();
						$('.full_rent_form').hide();
						$('.pravel_rent_date_field_form .single_add_to_cart_button').prop('disabled', true);
					}
					else
					{
						$('input[name="quantity"]').attr('max', left_qty);
						$('#pravel_book_product_qty').val(left_qty);
						$('#pravel_check_out_general_pro_2').removeClass('out-of-stock');
						$('#pravel_check_out_general_pro_2').addClass('in-stock');
						$('#pravel_check_out_general_pro_2').text(left_qty + ' in stock');
						
						$('.pravel_rent').removeClass('out-of-stock');
						$('.pravel_rent').addClass('in-stock');
						$('.pravel_rent').text(left_qty + ' in stock');
					}								
				}
				
			}
				
		  },
	});
	
	$('.pravel_front_start_date').click(function(e){
	    e.preventDefault();
	     var selected_option = $('#pravel_chosen_rent_option').val();
		
        if(selected_option == 1) {
            var selected_block = $('#block_days').val();
            if(selected_block == '' ) {
                $('#has_disabled_value').html('Please Select Block First.');
                $('.pravel_front_start_date').val('');
            } else {
                $('#has_disabled_value').html('');
            }
            
        }
	});
	
	var i = $("input[name = pravel_choose_option]:checked").val();
	if(i == 'pravel_buy') {
	    $('.block_options').hide();
	    $('.full_rent_form').hide();
	}

	$('.block-select-option').hide();
	$('.pravel_block_price').hide();
	
	
	    $('input[name = pravel_choose_rent_option]').change(function(){
	        $('#has_disabled_value').html('');
			var pravel_selected_option = $(this).val();
			$('#block_select_option').prop('selectedIndex',0);
			$('.pravel_block_price ins span.woocommerce-Price-amount').text('');
			$('#block_selected').val('');
			$('#block_price').val('');
			$('#block_days').val('');
			$('#block_price2').val('');
			$('.pravel_front_start_date').val('');
			$('.pravel_front_end_date').val('');
			$('#pravel_chosen_rent_option').val('');
			$('input[name=pravel_front_start_date], input[name=pravel_front_end_date]').val('');
			$(this).attr('checked','checked');
			var other = $('input[name = pravel_choose_rent_option]').not(this);
			other.removeAttr("checked");

			if(pravel_selected_option == 'pravel_block'){
			    $('#pravel_chosen_rent_option').val('1');
				$('.block-select-option').show();				
				$('.pravel_rent_price').hide();
				$('.pravel_block_price').show();
				$('.price:not(.pravel_block_price)').hide();
				$('input[name=pravel_front_end_date]').hide();
			} else {
				$('.block-select-option').hide();
				$('.pravel_rent_price').show();
				$('.price:not(.pravel_rent_price)').hide();
				$('.pravel_block_price').hide();
				$('input[name=pravel_front_end_date]').show();
			}
		});
    
   
	

	var symbol = $('.pravel_block_price ins span.woocommerce-Price-amount span.woocommerce-Price-currencySymbol').data('sym');
	
	$('dd.variation-pravel_rental_product_qty').each(function(){
		var pravel_qty_set_on_cart = $(this).children('p').text();		
		if(pravel_qty_set_on_cart != '')
		{			
			$(this).parent('.variation').parent('.product-name').parent('.woocommerce-cart-form__cart-item').children('.product-quantity').children('.quantity').children('input').attr('max' , pravel_qty_set_on_cart);
		}
		
	});
	
	$( document.body ).on( 'updated_cart_totals', function(){		
			$('dd.variation-pravel_rental_product_qty').each(function(){
				var pravel_qty_set_on_cart = $(this).children('p').text();		
				if(pravel_qty_set_on_cart != '')
				{			
					$(this).parent('.variation').parent('.product-name').parent('.woocommerce-cart-form__cart-item').children('.product-quantity').children('.quantity').children('input').attr('max' , pravel_qty_set_on_cart);
				}				
			});
			$('dd.variation-pravel_used_product_qty').each(function(){
				var pravel_qty_set_on_cart = $(this).children('p').text();	 
				if(pravel_qty_set_on_cart != '')
				{			
					$(this).parent('.variation').parent('.product-name').parent('.woocommerce-cart-form__cart-item').children('.product-quantity').children('.quantity').children('input').attr('max' , pravel_qty_set_on_cart);
				}
				
			});
	
	});
	
	
	
	$('.block_select_option').on('change', function(){

		$('.pravel_front_start_date').val('');
		$('.pravel_front_end_date').val('');
		$('#pravel_chosen_rent_option').val('');
		var selectedBlock = $(this).find(':selected').data('block');
		var seledtedPrice = $(this).find(':selected').val();
		var selectedDays = $(this).find(':selected').data('days');

        $('#has_disabled_value').html('');
        
		$('#block_selected').val(selectedBlock);
		$('#block_price').val(seledtedPrice);
		$('#block_price2').val(seledtedPrice);
		$('#block_days').val(selectedDays);

		$('.pravel_block_price ins span.woocommerce-Price-amount').text(symbol + seledtedPrice);

	});
	
	function formatDate(date) {
		 var d = new Date(date),
			 month = '' + (d.getMonth() + 1),
			 day = '' + d.getDate(),
			 year = d.getFullYear();

		 if (month.length < 2) month = '0' + month;
		 if (day.length < 2) day = '0' + day;

		 return [year, month, day].join('-');
	}
	
	function pravel_datediff(first, second) {		
		return Math.round((second-first)/(1000*60*60*24)) + 1;
	}
	
	function pravel_parseDate(str) {
		var mdy = str.split('/');
		return new Date(mdy[2], mdy[0]-1, mdy[1]);
	}
	
	$('.email_subscribe').on('click', function(){
	    var ajaxurl = ajax_custom.ajaxurl;
	    var email = $('#_custom_option').val();
	    var id = $('#_custom_option').data('id');
	    var userid = $('#_custom_option').data('user');
	    var producttype = $('#product_buy_type').val();
        var messageSuccess = 'You Have Successfully Registigered For Product Re-Stock Notification.';
	    var messageError = 'Sorry!!! There was Some Error. Please Try after some time.';

	    $.ajax({
			url : ajaxurl,
			type : 'POST',
			data : {
				email: email,
				id: id,
				userid: userid,
				producttype: producttype,				
				action : 'pravel_stock_notify'
			},
			beforeSend: function(){
                $("#loader").show();
            },
			error : function( response ){
				$('.buy_notify_form').append("<p class= 'errorMsg'>" + messageError + "</p>");
			},
			success : function( response ){
				$('.email_subscribe').attr("disabled", true);
				$('#_custom_option').attr("disabled", true);
			    $('.buy_notify_form').append("<p class= 'successMsg'>" + messageSuccess + "</p>");  
			},
			complete:function(data){               
                $("#loader").hide();
           }
	    });
	});

	$('.email_rent_subscribe').on('click', function(){
	    var ajaxurl = ajax_custom.ajaxurl;
	    var email = $('#_custom_rent_option').val();
	    var id = $('#_custom_rent_option').data('id');
	    var userid = $('#_custom_rent_option').data('user');
	    var start_date = $('#start_unavaiable_hidden_date').val();
	    var end_date = $('#end_unavaiable_hidden_date').val();
	    var producttype = $('#product_type').val();
	    var messageSuccess = 'You Have Successfully Registigered For Product Re-Stock Notification.';
	    var messageError = 'Sorry!!! There was Some Error. Please Try after some time.';   
	    $.ajax({
			url : ajaxurl,
			type : 'POST',
			data : {
				email: email,
				id: id,
				userid: userid,
				start_date: start_date,
				end_date: end_date,
				producttype: producttype,				
				action : 'pravel_rent_stock_notify'
			},
			beforeSend: function(){
                $("#loader").show();
            },
			error : function( response ){
				$('.notify_form').append("<p class= 'errorMsg'>" + messageError + "</p>");
			},
			success : function( response ){
			    $('.email_rent_subscribe').attr("disabled", true);
				$('#_custom_rent_option').attr("disabled", true);
			    $('.notify_form').append("<p class= 'successMsg'>" + messageSuccess + "</p>");
			    
			},
			complete:function(data){
                $("#loader").hide();
           }
	    });
	});

	$("#Notiyformlink").click(function() {  
         $(".notify_form").toggle('fast','swing');     
    });

    $("#ship-to-different-address-checkbox").change(function(){
        if($(this).is(':checked')){
            if($('#pickup_order_field input').is(':checked')) {               
                $("input[name='payment_method']:checkbox").prop('checked',false);
                 $('#payment_method_pickup').prop('checked', false);
                $('#payment_method_pickup').attr('checked', false);
                $('#payment_method_bacs').prop('checked', true);
                $('#payment_method_bacs').attr('checked', true);
                 $('#payment_method_pickup').trigger("click");
                $('#pickup_order').trigger("click");
            }
        }
    });
    
    $('#pickup_order_field input').change( function () {
        var checked = 0;
        if($(this).is(':checked')){
        	$('#pravel_delivery_selected').val('yes');
        	if($("#ship-to-different-address-checkbox").is(':checked')) {
        		$("#ship-to-different-address-checkbox").prop("checked", false);
        		$('.shipping_address').toggle();
        	}
        } else {
        	$('#pravel_delivery_selected').val('no');
        }
        
        var ajaxurl = ajax_custom.ajaxurl;
        if ( $('#pickup_order').is(':checked') ){
            checked = 1;
        }

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'pravel_get_pickup_data',
                'pickup_order': checked,
            },
            success: function (result) {
                $('body').trigger('update_checkout');
                console.log('response: '+result);
            },
            error: function(error){
                console.log(error);
            }
        });
    });
});