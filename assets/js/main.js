jQuery(document).ready(function( $ ) {
	//Create Repeater
	$("#pravel_repeater").createRepeater({
		showFirstItemToDefault: true,
	});

	$("#pravel_block_repeater").createRepeater({
		showFirstItemToDefault: true,
	});

	$("#pravel_repeater_custom_price").createRepeater({
		showFirstItemToDefault: true,
	});
	
	
	$("#pravel_repeater_custom_price_days").createRepeater({
		showFirstItemToDefault: true,
	});
	
	$("#pravel_repeater_custom_price_dates").createRepeater({
		showFirstItemToDefault: true,
	});
	
	$('#pravel_data_table').DataTable({
		"searching": false,
		"lengthChange": false,
	});
	
	$('#pravel_data_table_future').DataTable({
		"searching": false,
		"lengthChange": false,
	});
	
	$('#pravel_data_table_past').DataTable({
		"searching": false,
		"lengthChange": false,
	});
	

	$('.pravel_rental_tab_hide').hide();
	$('.pravel_used_tab_hide').hide();
	
	//check bokking option on or not and display relative fields
	$('#pravel_booking_sale_price').keyup(function(){
		var sale_price = parseFloat($(this).val());			
		console.log(sale_price);
		var reg_price = parseFloat($('#pravel_booking_reg_price').val());		
		console.log(reg_price);
		if(sale_price >= reg_price || reg_price == '' || sale_price <= 0)
		{
			$('#pravel_price_error').text('Please Enter in a value less than regular price value');
			$('#pravel_price_error').show();
			$(this).val('');
		}
		else
		{
			$('#pravel_price_error').hide();
		}		
	});
	
	
	$('.pravel_close').click(function(){
		$('#mypravelpopup').css('display', 'none');
	});
	
	window.onclick = function(event) {
	  var mypravelpopup = document.getElementById("mypravelpopup");
	  if (event.target == mypravelpopup) {
		$('#mypravelpopup').css('display', 'none');
	  }
	}
	
	$('#pravel_booking_reg_price').keyup(function(){
		$('#pravel_booking_sale_price').val('');
		var reg_price = $(this).val();
		if(reg_price <= 0)
		{
			$('#pravel_price_error').text('Please Enter in a value greater than 0');
			$('#pravel_price_error').show();
			$(this).val('');
		}
		else
		{
			$('#pravel_price_error').hide();
		}
	});
	var old_min_val = $('#pravel_min_booking_req').val();
	$('#pravel_min_booking_req').keyup(function(){
		var check_block = $('#pravel_manage_add_block').prop('checked');	
		var min_booking_days = $(this).val();		
		var input_days_block = $('#pravel_block_repeater tbody tr.items:nth-child(2) td input.pravel_block-days').val();		
		if(min_booking_days <= 0)
		{
			$('#pravel_min_book_error').text('Please Enter in a value greater than 0');
			$('#pravel_min_book_error').show();
			$(this).val('');
			$('input.pravel_block-days').attr('min','');
		}
		else if(min_booking_days % 1 != 0)
		{
			$('#pravel_min_book_error').text('Please Enter only integer value');
			$('#pravel_min_book_error').show();
			$(this).val('');
			$('input.pravel_block-days').attr('min',1);
		}
		else if(check_block == true){
			$('input.pravel_block-days').attr('min',min_booking_days);
		}
		else
		{
			$('#pravel_min_book_error').hide();			
		}
		
	});
	
	$('.pravel_block-price').keyup(function(){
		var bPrice = $(this).val();
		if(bPrice <= 0) {
		    alert('Please Enter in a value greater than 0');
			$(this).val('');
		}
		else if(bPrice % 1 !== 0)
		{
			alert('Please Enter only integer value');
			$(this).val('');
		}
		else
		{
			$('#pravel_min_book_error').hide();
		}
	});

	
	
	$('#pravel_manage_rent_stock').change(function(){
		var text = $(this).data('text');
		var video = $(this).data('video');
		$('#mypravelpopup .pravel_popup_main .pravel_popup_right h2').html(text);
		$('#mypravelpopup .pravel_popup_main .pravel_popup_right a').attr("href", video);
		$('#mypravelpopup').css('display', 'flex');
		$(this).prop('checked',false);
	});
	
	
	$('#pravel_booking_extra_price_option').change(function(){
		var text = $(this).data('text');
		var video = $(this).data('video');
		$('#mypravelpopup .pravel_popup_main .pravel_popup_right h2').html(text);
		$('#mypravelpopup .pravel_popup_main .pravel_popup_right a').attr("href", video);
		$('#mypravelpopup').css('display', 'flex');
		$(this).prop('checked',false);
	});
	

	$('#pravel_manage_add_block').change(function(){
		if($(this).is(":checked")) {
			$('input.pravel_block-name').prop('required', true);
			$('input.pravel_block-days').prop('required', true);
			$('input.pravel_block-price').prop('required', true);
			var min_qty = $('#pravel_min_booking_req').val();
			if(min_qty === ''){
				min_qty = 1;
			}
			$('input.pravel_block-days').attr('min',min_qty);		
		}
		else
		{
			$('input.pravel_block-name').prop('required', false);
			$('input.pravel_block-days').prop('required', false);
			$('input.pravel_block-price').prop('required', false);
			$('input.pravel_block-days').attr('min','');
		}
	});
	
	$('#pravel_admin_booking_option').change(function(){
		
		if($(this).is(":checked")) {
			var product_option = $('input[type=radio][name=pravel_product_option]:checked').val();
			if(product_option == 'pravel_only_rent')
			{				
				$('.options_group.pricing').hide();				
				$('#pravel_used_price_option').hide();
				$('#pravel_used_product_set_option').hide();
				$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
			    $('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');
			}
			
			$('#pravel_rental_price_option').show();
			$('#prave_prduct_option_set').show();			
			$('.pravel_rental_option_options').removeClass('pravel_rental_tab_hide');
			$('.pravel_rental_option_options').addClass('pravel_rental_tab_show');
			$('.pravel_booked_data_options').removeClass('pravel_rental_tab_hide');
			$('.pravel_booked_data_options').addClass('pravel_rental_tab_show');
			$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_hide');
			$('.pravel_used_product_data_options').addClass('pravel_rental_tab_show');	
			$('#pravel_booking_reg_price').prop('required', true);						
		}
		else{
			
			$('#pravel_rental_price_option').hide();
			$('#prave_prduct_option_set').hide();		
			$('.options_group.pricing').show();
			$('._regular_price_field').show();
			$('._sale_price_field').show();
			$('.pravel_rental_option_options').removeClass('pravel_rental_tab_show');
			$('.pravel_rental_option_options').addClass('pravel_rental_tab_hide');
			$('.pravel_booked_data_options').removeClass('pravel_rental_tab_show');
			$('.pravel_booked_data_options').addClass('pravel_rental_tab_hide');
			$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
			$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');			
			$('.general_options a').click();
			$('#pravel_booking_reg_price').prop('required', false);	
			$('#pravel_used_product_set_option').hide();			
			$('#pravel_used_option').hide();	
			$("#pravel_used_product_option").prop("checked", false);			
		}
		
	});
	
	
	$('#_virtual, #_downloadable').change(function(){
		if($('#_virtual').is(":checked") || $('#_downloadable').is(":checked")) {
			$('#pravel_rental_price_option').hide();
			$('#prave_prduct_option_set').hide();		
			$('.options_group.pricing').show();
			$('._regular_price_field').show();
			$('._sale_price_field').show();
			$('.pravel_rental_option_options').removeClass('pravel_rental_tab_show');
			$('.pravel_rental_option_options').addClass('pravel_rental_tab_hide');
			$('.pravel_booked_data_options').removeClass('pravel_rental_tab_show');
			$('.pravel_booked_data_options').addClass('pravel_rental_tab_hide');
			$('#pravel_booking_reg_price').prop('required', false);	
			$('#pravel_used_product_set_option').hide();			
			$('#pravel_used_option').hide();	
			$("#pravel_used_product_option").prop("checked", false);
			$('label[for=pravel_admin_booking_option]').hide();
		}
		else
		{
			if($('#pravel_admin_booking_option').is(":checked")) {
				
				var product_option = $('input[type=radio][name=pravel_product_option]:checked').val();
				if(product_option == 'pravel_only_rent')
				{				
					$('.options_group.pricing').hide();				
					$('#pravel_used_price_option').hide();
					$('#pravel_used_product_set_option').hide();
					$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
			    	$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');
				}
				
				$('#pravel_rental_price_option').show();
				$('#prave_prduct_option_set').show();				
				$('.pravel_rental_option_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_rental_option_options').addClass('pravel_rental_tab_show');
				$('.pravel_booked_data_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_booked_data_options').addClass('pravel_rental_tab_show');
				$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_used_product_data_options').addClass('pravel_rental_tab_show');		
				$('#pravel_booking_reg_price').prop('required', true);			
			
			}
			else{
				$('.pravel_rental_option_options').removeClass('pravel_rental_tab_show');
				$('.pravel_rental_option_options').addClass('pravel_rental_tab_hide');
				$('.pravel_booked_data_options').removeClass('pravel_rental_tab_show');
				$('.pravel_booked_data_options').addClass('pravel_rental_tab_hide');
				$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
				$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');	
			}
			$('label[for=pravel_admin_booking_option]').show();
		}
		
	});
	
	
	$('#product-type').change(function(){		
		var selectedProducttype = $('#product-type option:selected').val();		
		if(selectedProducttype == 'simple')
		{
			if($('#pravel_admin_booking_option').is(":checked")) {
				var product_option = $('input[type=radio][name=pravel_product_option]:checked').val();
				if(product_option == 'pravel_only_rent')
				{				
					$('.options_group.pricing').hide();				
					$('#pravel_used_price_option').hide();
					$('#pravel_used_product_set_option').hide();
					$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
			    	$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');
				}
				
				$('#pravel_rental_price_option').show();
				$('#prave_prduct_option_set').show();				
				$('.pravel_rental_option_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_rental_option_options').addClass('pravel_rental_tab_show');
				$('.pravel_booked_data_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_booked_data_options').addClass('pravel_rental_tab_show');
				$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_hide');
				$('.pravel_used_product_data_options').addClass('pravel_rental_tab_show');	
				$('#pravel_booking_reg_price').prop('required', true);
				$('label[for=pravel_admin_booking_option]').show();			
			}
			else
			{
				$('.pravel_rental_option_options').removeClass('pravel_rental_tab_show');
				$('.pravel_rental_option_options').addClass('pravel_rental_tab_hide');
				$('.pravel_booked_data_options').removeClass('pravel_rental_tab_show');
				$('.pravel_booked_data_options').addClass('pravel_rental_tab_hide');
				$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
				$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');	
			}
			$('label[for=pravel_admin_booking_option]').show();
		}
		else
		{			
			$('#pravel_rental_price_option').hide();
			$('#prave_prduct_option_set').hide();		
			$('.options_group.pricing').show();
			$('._regular_price_field').show();
			$('._sale_price_field').show();
			$('.pravel_rental_option_options').removeClass('pravel_rental_tab_show');
			$('.pravel_rental_option_options').addClass('pravel_rental_tab_hide');
			$('.pravel_booked_data_options').removeClass('pravel_rental_tab_show');
			$('.pravel_booked_data_options').addClass('pravel_rental_tab_hide');
			$('.pravel_used_product_data_options').removeClass('pravel_rental_tab_show');
			$('.pravel_used_product_data_options').addClass('pravel_rental_tab_hide');
			$('#pravel_booking_reg_price').prop('required', false);	
			$('#pravel_used_product_set_option').hide();			
			$('#pravel_used_option').hide();	
			$("#pravel_used_product_option").prop("checked", false);
			$('label[for=pravel_admin_booking_option]').hide();
			
		}
		
	});
	
	//Change hide show reletive field
	$('input[type=radio][name=pravel_product_option]').change(function() {		
		if(this.value == 'pravel_only_rent'){
			$('.options_group.pricing').hide('slow', 'swing');
			$('#pravel_used_product_set_option').hide('slow', 'swing');
			$('#pravel_used_price_option').hide('slow', 'swing');	
			$('#pravel_used_option').hide('slow', 'swing');	
		}
		else{
			var text = $(this).data('text');
			var video = $(this).data('video');
			$('#mypravelpopup .pravel_popup_main .pravel_popup_right h2').html(text);
			$('#mypravelpopup .pravel_popup_main .pravel_popup_right a').attr("href", video);			
			$('#mypravelpopup').css('display', 'flex');
			$('input[type=radio][name=pravel_product_option][value=pravel_only_rent]').prop('checked',true);
		}
	});

	$('#pravel_used_product_option').change(function(){
	   $('#mypravelpopup').css('display', 'flex');
	   $(this).prop('checked',false);
	});
	
	$('input[type=radio][name=pravel_booking_sorting]').change(function() {		
		if(this.value == 'pravel_all_booking'){
			$('#pravel_data_table_div').show();
			$('#pravel_data_table_future_div').hide();
			$('#pravel_data_table_past_div').hide();
		}
		else if(this.value == 'pravel_future_booking'){
			$('#pravel_data_table_div').hide();
			$('#pravel_data_table_future_div').show();
			$('#pravel_data_table_past_div').hide();
		}
		else if(this.value == 'pravel_past_booking'){
			$('#pravel_data_table_div').hide();
			$('#pravel_data_table_future_div').hide();
			$('#pravel_data_table_past_div').show();
		}
		
	});

	//Admin booking datepicker
	var dateToday = new Date();	
	
	$('.items').each(function(){
		var dates = $(this).children('td').children(".pravel_start-date, .pravel_end-date").datepicker({			  
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			minDate: dateToday,	
			onSelect: function(selectedDate) {
				var name_attr = $(this).attr('date-type'); 
				if(name_attr == 'pravel_start-date'){					
					var option = 'minDate';	
					$(this).parent('td').parent('.items').children('td').children(".pravel_end-date").val('');				
				}
				else
				{
					var option = 'maxDate';
				}	
				var instance = $(this).data("datepicker"),
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);	
			}			
		});
	});
	
	
	$('.repeater-add-btn').click(function() {  	
		$('input.pravel_start-date').slice(-1).val('');
		$('input.pravel_end-date').slice(-1).val('');
		var dateToday = new Date();
		var dates =$(".pravel_start-date:last, .pravel_end-date:last").datepicker({				  
		  changeMonth: true,
		  numberOfMonths: 1,
		  minDate: dateToday,
		  onSelect: function(selectedDate) {
				var name_attr = $(this).attr('date-type'); 				
				if(name_attr == 'pravel_start-date'){
					var option = 'minDate';
					$(this).parent('td').parent('.items').children('td').children(".pravel_end-date").val('');			
				}
				else
				{
					var option = 'maxDate';
				}					
				var instance = $(this).data("datepicker"),
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
		  }
		});
	
	});
	
	$('.custom_price_date').each(function(){
		var dates = $(this).children('td').children(".pravel_custom_price_start_date, .pravel_custom_price_end_date").datepicker({			  
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			minDate: dateToday,	
			onSelect: function(selectedDate) {
				var name_attr = $(this).attr('date-type'); 
				if(name_attr == 'pravel_custom_price_start_date')
				{					
					var option = 'minDate';	
					$(this).parent('td').parent('.custom_price_date').children('td').children(".pravel_custom_price_end_date").val('');				
				}
				else
				{
					var option = 'maxDate';
				}	
				var instance = $(this).data("datepicker"),
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);	
			}			
		});
	});
	
	
	//Repeater Date field for disable date
	$('.repeater-add-btn_custom_price').click(function() { 
		
		$('input.pravel_custom_price_start_date').slice(-1).val('');
		$('input.pravel_custom_price_end_date').slice(-1).val('');
		$('input.pravel_custom_price_date').slice(-1).val('');
		
		var dateToday = new Date();
		var dates =$(".pravel_custom_price_start_date:last, .pravel_custom_price_end_date:last").datepicker({				  
		  changeMonth: true,
		  numberOfMonths: 1,
		  minDate: dateToday,
		  onSelect: function(selectedDate) {
				var name_attr = $(this).attr('date-type'); 				
				if(name_attr == 'pravel_custom_price_start_date')
				{
					var option = 'minDate';
					$(this).parent('td').parent('.custom_price_date').children('td').children(".pravel_custom_price_end_date").val('');			
				}
				else
				{
					var option = 'maxDate';
				}					
				var instance = $(this).data("datepicker"),
				date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
		  }
		});
	
	});
	
	$('.repeater-add-btn_custom_price_2').click(function() { 
		$('input.pravel_custom_price_days_price').slice(-1).val('');
		$('select.pravel_custom_price_days').slice(-1).val($(".pravel_custom_price_days option:first").val());
		
		
	});
	
	$('.repeater-add-btn_custom_price_3').click(function() { 
		$('input.pravel_custom_price_dates_eve_month_price').slice(-1).val('');
		$('select.pravel_custom_price_dates_eve_month').slice(-1).val($(".pravel_custom_price_dates_eve_month option:first").val());		
	});
	
	$('.repeater-add-block-btn').click(function(){
		$('input.pravel_block-name').slice(-1).val('');
		$('input.pravel_block-days').slice(-1).val('');
		$('input.pravel_block-price').slice(-1).val('');

		$('input.pravel_block-name').slice(-1).prop('required','true');
		$('input.pravel_block-days').slice(-1).prop('required','true');
		$('input.pravel_block-price').slice(-1).prop('required','true');
		var min_qty = $('#pravel_min_booking_req').val();
		$('input.pravel_block-days').attr('min',min_qty);	
		
	});



	if($('#pravel_manage_add_block').is(":checked")) {
		$('#pravel_block_repeater').show();
	} else {
		$('#pravel_block_repeater').hide();
	}

	$('#pravel_manage_add_block').change(function(){
		if($(this).is(":checked")){
			$('#pravel_block_repeater').show();
		} else {
			$('#pravel_block_repeater').hide();
		}
	});

	if($('#woocommerce_notify_email').is(":checked")){
        $('.product-mail').show();
            
    }
    else {
        $('.product-mail').hide();
    }
	
	$('#woocommerce_notify_email').on('click', function(){
	    
            $('.product-mail').toggle('slow','swing');
            
	});
	
	var mediaUploader1
	$( '#upload_used_img' ).on('click', function(e) {

		e.preventDefault();

		if( mediaUploader1 ) {
			mediaUploader1.open();
			return;
		}

		mediaUploader1 = wp.media.frames.file_frame = wp.media({
			title: 'Choose a Logo',
			button: {
				text: 'Choose Logo'
			},
			multiple: false
		});

		mediaUploader1.on('select', function(){
			attachment= mediaUploader1.state().get('selection').first().toJSON();
			$('#used_img').val(attachment.url);
			$( '#logo-re' ).css('background-image', 'url('+ attachment.url +')');
		});

		mediaUploader1.open();

	});

	$('#remove-logo').on('click', function(e) {
		e.preventDefault();
		var answer = confirm("Are you Sure you want to Delete Profile Picture?");
		if(answer == true){
			$('#used_img').val('');
			$( "#publish" ).click();
		} 
		return;
	});

	if($('#pravel_notify_email').is(":checked")){
		$('#pravel_notify_email_buy_recepient').removeAttr('disabled');
		$('#pravel_notify_email_buy_subject').removeAttr('disabled');
		$('#pravel_notify_email_buy_message').removeAttr('disabled');
		$('#pravel_notify_email_rent_recepient').removeAttr('disabled');
		$('#pravel_notify_email_rent_subject').removeAttr('disabled');
		$('#pravel_notify_email_rent_message').removeAttr('disabled');
	} else {
		$('#pravel_notify_email_buy_recepient').prop("disabled", true);
		$('#pravel_notify_email_buy_subject').prop("disabled", true);
		$('#pravel_notify_email_buy_message').prop("disabled", true);
		$('#pravel_notify_email_rent_recepient').prop("disabled", true);
		$('#pravel_notify_email_rent_subject').prop("disabled", true);
		$('#pravel_notify_email_rent_message').prop("disabled", true);
	}

	$('#pravel_notify_email').on('change', function(e) {
		if($('#pravel_notify_email').is(":checked")) {
			$('#pravel_notify_email_buy_recepient').removeAttr('disabled');
			$('#pravel_notify_email_buy_subject').removeAttr('disabled');
			$('#pravel_notify_email_buy_message').removeAttr('disabled');
			$('#pravel_notify_email_rent_recepient').removeAttr('disabled');
			$('#pravel_notify_email_rent_subject').removeAttr('disabled');
			$('#pravel_notify_email_rent_message').removeAttr('disabled');
		} else {
			$('#pravel_notify_email_buy_recepient').prop("disabled", true);
			$('#pravel_notify_email_buy_subject').prop("disabled", true);
			$('#pravel_notify_email_buy_message').prop("disabled", true);
			$('#pravel_notify_email_rent_recepient').prop("disabled", true);
			$('#pravel_notify_email_rent_subject').prop("disabled", true);
			$('#pravel_notify_email_rent_message').prop("disabled", true);
		}
		
	});

	if($('#pravel_pickup_enable_field').is(":checked")) {
		$('#pravel_alternative_store_field').prop("disabled", false);
		$('#pravel_store_name').attr('disabled','disabled');
		$('#pravel_store_address_1').attr('disabled','disabled');
		$('#pravel_store_address_2').attr('disabled','disabled');
		$('#pravel_store_city').attr('disabled','disabled');
		$('#pravel_store_zip').attr('disabled','disabled');
		
	} else {
		$('#pravel_alternative_store_field').prop("disabled", true);
	}
	
	$('#pravel_pickup_enable_field').on('change', function(e) {
		if($('#pravel_pickup_enable_field').is(":checked")) {
			$('#pravel_alternative_store_field').prop("disabled", false);
		} else {
			$('#pravel_alternative_store_field').prop("disabled", true);
			$('#pravel_store_name').attr('disabled','disabled');
			$('#pravel_store_address_1').attr('disabled','disabled');
			$('#pravel_store_address_2').attr('disabled','disabled');
			$('#pravel_store_city').attr('disabled','disabled');
			$('#pravel_store_zip').attr('disabled','disabled');
		}
		
	});

	if($('#pravel_alternative_store_field').is(":checked")) {
		$('#pravel_store_name').removeAttr('disabled');
		$('#pravel_store_address_1').removeAttr('disabled');
		$('#pravel_store_address_2').removeAttr('disabled');
		$('#pravel_store_city').removeAttr('disabled');
		$('#pravel_store_zip').removeAttr('disabled');
	} else {
		$('#pravel_store_name').attr('disabled','disabled');
		$('#pravel_store_address_1').attr('disabled','disabled');
		$('#pravel_store_address_2').attr('disabled','disabled');
		$('#pravel_store_city').attr('disabled','disabled');
		$('#pravel_store_zip').attr('disabled','disabled');
	}

	$('#pravel_alternative_store_field').on('change', function(e) {
		if($('#pravel_alternative_store_field').is(":checked")) {
			$('#pravel_store_name').removeAttr('disabled');
			$('#pravel_store_address_1').removeAttr('disabled');
			$('#pravel_store_address_2').removeAttr('disabled');
			$('#pravel_store_city').removeAttr('disabled');
			$('#pravel_store_zip').removeAttr('disabled');
			
		} else {
			$('#pravel_store_name').attr('disabled','disabled');
			$('#pravel_store_address_1').attr('disabled','disabled');
			$('#pravel_store_address_2').attr('disabled','disabled');
			$('#pravel_store_city').attr('disabled','disabled');
			$('#pravel_store_zip').attr('disabled','disabled');
		}
		
	});
});



function change_days_disable(x){
	
	var select_value_1 = x.value;
	var select_value_name = x.name;
	jQuery('select.pravel_custom_price_days').children('option').attr('disabled', false);
	var repeater = $('#pravel_repeater_custom_price_days');
	var items = repeater.find(".items");	
	var k = 0;			
	items.each(function (index, item) {		
		var select_value = jQuery(this).children('td').children('select').val();		
		if(select_value_1 == select_value)
		{
			k++;		
		}	
	});
	if(k > 1){
		alert('You have selected this value already');	
		$('select[name = "'+select_value_name+'"]').val(jQuery('select[name = "'+select_value_name+'"] option:first').val());
	}
}


function change_date_disable(x){	
	var select_value_1 = x.value;
	var select_value_name = x.name;
	jQuery('select.pravel_custom_price_dates_eve_month').children('option').attr('disabled', false);
	var repeater = $('#pravel_repeater_custom_price_dates');
	var items = repeater.find(".items");	
	var k = 0;			
	items.each(function (index, item) {
		var select_value = jQuery(this).children('td').children('select').val();		
		if(select_value_1 == select_value)
		{
			k++;		
		}		
	}); 
	if(k > 1){
		alert('You have selected this value already');	
		$('select[name = "'+select_value_name+'"]').val(jQuery('select[name = "'+select_value_name+'"] option:first').val());
	}
	
}
function check_price_val(x, err_class){	
	console.log(err_class);
	var select_value = x.value;
	var select_value_name = x.name;	
	if(select_value <= 0)
	{
		jQuery('#'+err_class).text('Please Enter in a value grater than 0');
		jQuery('#'+err_class).show();
		jQuery('input[name = "'+select_value_name+'"]').val('');
	}
	else
	{
		jQuery('#'+err_class).text('');
		jQuery('#'+err_class).hide();
	}
	

}

