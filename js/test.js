$(document).ready(function(){
	$('#sel2').hide();
	$('#sel3').hide(); 
	$('#sel4').hide();
	$('#textar1').hide();
	$('#date1').hide();
	$('#phone1').hide();
	$.ajax({
		type: "POST",
		type: "json",
		url: "task_types_get.php",
		success: function(msg){
			msg = JSON.parse(msg);
			$.each(msg, function(key, value){
				
				$('#sel4').append($('<option>', {
					value: key,
					text: value,
				}));
			})
		}
	});
	$( "#sel3" ).change(function() {
		if($('#sel3').find(":selected").val()==4){
			$('#textar1').show();
			$('#phone1').hide();
		}
		else{
			$('#textar1').hide();
			$('#phone1').show();
		}
	});
	$ ( "#sel1" ).change(function() {
		switch ($('#sel1').find(":selected").val()) {
			case '1': 
				$('#sel2').hide();
				$('#sel3').hide(); 
				$('#sel4').hide();
				$('#textar1').hide();
				$('#date1').hide();
				$('#phone1').hide();
				$("#num1").attr("placeholder","количество элементов");
				break;

			case '2': 
				$('#sel2').show();
				$('#sel3').hide(); 
				$('#sel4').hide();
				$('#textar1').show();
				$('#date1').hide();
				$('#phone1').hide();
				$("#num1").attr("placeholder","введите id");
				break;

			case '3':
				$('#sel2').show();
				$('#sel3').show();
				$('#sel4').hide();
				$('#date1').hide();      
				if($('#sel3').find(":selected").val()==4){
					$('#textar1').show();
					$('#phone1').hide();
				}
				else{
					$('#textar1').hide();
					$('#phone1').show();
				}
				$("#num1").attr("placeholder","введите id");
				break;

			case '4':
				$('#sel2').show();
				$('#sel3').hide(); 
				$('#sel4').show();
				$('#textar1').show();
				$('#date1').show();
				$('#phone1').hide();
				$("#num1").attr("placeholder","введите id");
				break;

			case '5': 
				$('#sel2').hide();
				$('#sel3').hide(); 
				$('#sel4').hide();
				$('#textar1').show();
				$('#date1').hide();
				$('#phone1').hide();
				$("#num1").attr("placeholder","введите id");
				break;

			default:
				$('#sel2').hide();
				$('#sel3').hide(); 
				$('#sel4').hide();
				$('#textar1').hide();
				$('#date1').hide();
				$('#phone1').hide();
				$("#num1").attr("placeholder","введите id");
				break;
		};
	});

	var current_date = new Date();
	var ndate = parseInt(-current_date.getTimezoneOffset() / 60);
	$("#but1").on("click", function(){
		let link;
		let ajdata;
		switch ($('#sel1').find(":selected").val()) {
			case '1': 
				if ($("#num1").val()<=0 || $("#num1").val()>10000){
					alert('значение должно быть положительным и меньше 10000');
					return false;
				};
				link = "amo_mass_create.php";
				ajdata = "num="+$("#num1").val();
				break;

			case '2': 
				if ( !$('#num1').val() || !$('#textar1').val()) {                  
					alert('заполните все поля');
					return false;
				};
				link = "textfield_create.php";
				ajdata = "id="+$("#num1").val()+"&elem_type="+$("#sel2").find(":selected").val()+"&text="+$("#textar1").val();
				break;

			case '3':

				link = "event_add.php";
				ajdata = "id="+$("#num1").val()+"&elem_type="+$('#sel2').find(":selected").val();
				if ($('#sel3').find(":selected").val()==4) {
					if ( !$('#num1').val() || !$('#textar1').val()) {                  
						alert('заполните все поля');
					return false;
					};
					ajdata+="&note_type="+$('#sel3').find(":selected").val()+"&text="+$("#textar1").val();
				} else {
					if( !$('#num1').val() || !$('#phone1').val()) {                  
						alert('заполните все поля');
					return false;
					};
					ajdata+="&note_type="+$('#sel3').find(":selected").val()+"&text="+$("#phone1").val();

				}
				break;

			case '4':
				if ( !$('#num1').val() || !$('#textar1').val() || !$('#date1').val()) {                  
						alert('заполните все поля');
					return false;
				};
				link = "task_add.php";
				ajdata = "id="+$("#num1").val()+"&elem_type="+$('#sel2').find(":selected").val()+"&text="+$("#textar1").val()
				+"&task_type="+$('#sel4').find(":selected").val()+"&date="+$("#date1").val()+"T"+ndate;
				break;

			case '5': 
				if (!$("#num1").val()) {
					alert('заполните все поля');
					return false;
				};
				link = "task_close.php";
				ajdata = "id="+$("#num1").val()+"&text="+$("#textar1").val();
				break;

			default:
				return false;
				break;
		};
		$.ajax({
				type: "POST",
				data: ajdata,
				url: link,
				beforeSend: function(){
					$('#but1').attr('disabled', true);
				},
				success: function(msg){
					alert(msg);
				},
				complete: function(){
					$('#but1').attr('disabled', false);
				}
		});
	});
});


//amo_mass_create.php
//textfield_create.php
//event_add.php
//task_types_get.php
//task_add.php
//task_close.php